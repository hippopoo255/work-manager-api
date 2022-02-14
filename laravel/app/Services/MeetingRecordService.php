<?php

namespace App\Services;

use App\Contracts\Repositories\MeetingRecordRepositoryInterface as Repository;
use App\Contracts\Queries\MeetingRecordQueryInterface as Query;
use App\Contracts\Repositories\UserRepositoryInterface as UserRepository;
use App\Models\ActionType;
use App\Models\MeetingDecision;
use App\Services\MeetingDecisionService;
use App\Services\Supports\WithRepositoryTrait;
use App\Models\MeetingRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MeetingRecordJoinedNotification;
use App\Services\Supports\NotifySupport;
use App\Jobs\MeetingRecordJoinedActivityJob;
use App\Jobs\Supports\JobSupport;

class MeetingRecordService extends Service
{
  use WithRepositoryTrait;

  protected $meetingDecisionService;
  protected $userRepository;
  protected $actionTypeRepository;
  protected $activityRepository;
  protected $jobSupport;
  /**
   * UserService constructor.
   * @param Repository $repository
   * @param Query $query
   * @param MeetingDecisionService $meetingDecisionService
   * @param UserRepository $userRepository
   * @param JobSupport $jobSupport
   * @param MessageSentActivityJob $job
   */
  public function __construct(
    Repository $repository,
    Query $query,
    MeetingDecisionService $meetingDecisionService,
    UserRepository $userRepository,
    JobSupport $jobSupport,
    MeetingRecordJoinedActivityJob $job
  ) {
    $this->setRepository($repository);
    $this->setQuery($query);
    $this->meetingDecisionService = $meetingDecisionService;
    $this->userRepository = $userRepository;
    $this->jobSupport = $jobSupport;
    $this->jobSupport->init($job, 'meeting_record_joined');
  }

  /**
   * @param array $params
   * @param array|null $relation
   * @param int|null $perPage
   * @return array
   */
  public function paginate(array $params, ?int $perPage = null, ?array $relation = null)
  {
    $paginator = $this->query()->paginate($params, $perPage, $relation);
    if ($paginator) {
      $yearMonthAddedArr = json_decode(json_encode($paginator), true);
      $yearMonthAddedArr['year_month'] = $this->yearMonth();
      $yearMonthAddedArr['query_params'] = $params;
      return $yearMonthAddedArr;
    }
    return json_decode(json_encode($paginator), true);
  }

  /**
   * @return array
   */
  private function yearMonth(): array
  {
    $diff = $this->diffBetweenMonth();
    $yearMonth = [];
    for ($i = 0; $i < $diff; $i++) {
      $c = Carbon::parse("-{$i} month");
      $yearMonth[] = [
        'value' => $c->format('Y-m'),
        'label' => $c->format('Y年n月'),
      ];
    }
    return $yearMonth;
  }

  /**
   * @return int
   */
  private function diffBetweenMonth()
  {
    $latest = Carbon::parse($this->query()->oldestMeetingDate());
    return $latest->diffInMonths(Carbon::now());
  }

  /**
   * @param $id
   * @return MeetingRecord
   */
  public function find($id): MeetingRecord
  {
    return $this->repository()->find($id, [
      'members',
      'decisions.decidedBy',
      'decisions.createdBy',
      'decisions.tasks',
      'createdBy',
      'place',
    ]);
  }

  /**
   * @param array $params
   * @return MeetingRecord
   */
  public function store(array $params): MeetingRecord
  {
    // 議事録の保存
    $meetingRecord = $this->repository()->saveWithMembers($params);
    // 決議事項の保存
    if (isset($params['meeting_decisions'])) {
      foreach ($params['meeting_decisions'] as $meetingDecisionParams) {
        $this->saveDecisionByRecord($meetingDecisionParams, $meetingRecord);
      }
    }
    $meetingRecord->load(MeetingRecord::RELATIONS_ARRAY);

    Notification::send($meetingRecord->members->filter(function ($member) use ($meetingRecord) {
      return NotifySupport::shouldSend($member, $meetingRecord->created_by, ActionType::MEETING_RECORD_JOINED_KEY);
    }), new MeetingRecordJoinedNotification($meetingRecord));

    $this->jobSupport->dispatch($meetingRecord);

    return $meetingRecord;
  }

  /**
   * @param array $params
   * @param $id
   * @return MeetingRecord
   */
  public function update(array $params, $id): MeetingRecord
  {
    // 議事録の保存
    $meetingRecord = $this->repository()->saveWithMembers($params, 'members', $id);
    if (isset($params['meeting_decisions'])) {
      foreach ($params['meeting_decisions'] as $meetingDecisionParams) {
        $this->saveDecisionByRecord($meetingDecisionParams, $meetingRecord);
      }
      $meetingRecord->load($this->query()->relation());
    }
    return $meetingRecord;
  }

  /**
   * @param array $queryParams
   * @param array $params
   * @param int $perPage
   * @return array
   */
  public function delete($id, $queryParams, int $perPage): array
  {
    $deleted = $this->repository()->delete($id);
    return $this->paginate($queryParams, $perPage);
  }

  /**
   * 決議事項のフォームは議事録のフォームに包含されている
   * idがあってupdate(1)かdelete(2)のフラグが立っていれば、updateOrDelete()
   * フラグはないけどtasksがあればtask保存の処理
   * idがない場合は新規保存
   * @param array $meetingDecisionParams
   * @param MeetingRecord $meetingRecord
   * @return MeetingDecision
   */
  private function saveDecisionByRecord(array $meetingDecisionParams, MeetingRecord $meetingRecord): MeetingDecision
  {
    if (isset($meetingDecisionParams['id']) && isset($meetingDecisionParams['flag'])) {
      return $this->meetingDecisionService->updateOrDelete($meetingDecisionParams, $meetingRecord, $meetingDecisionParams['id']);
    } elseif (isset($meetingDecisionParams['id']) && isset($meetingDecisionParams['tasks'])) {
      $meetingDecision = $this->meetingDecisionService->find($meetingDecisionParams['id']);
      return $this->meetingDecisionService->saveTasksByDecision($meetingDecisionParams, $meetingDecision);
    } else {
      return $this->meetingDecisionService->store($meetingDecisionParams, $meetingRecord);
    }
  }

  /**
   * @param $id
   * @return MeetingRecord[]
   */
  public function findByUser($id = null): array
  {
    if ($id === null) {
      $id = Auth::user()->id;
    }
    $user = $this->userRepository->find($id, ['joinedMeetings.createdBy', 'joinedMeetings.place']);
    return $user->joinedMeetings->sortByDesc('id')->splice(0, 5)->all();
  }

  /**
   * @return int[]
   */
  public function ids(): array
  {
    return $this->repository()->ids();
  }
}
