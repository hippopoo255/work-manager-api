<?php

namespace App\Services;

use App\Contracts\Queries\ScheduleQueryInterface as Query;
use App\Contracts\Repositories\ScheduleRepositoryInterface as Repository;
use App\Contracts\Repositories\UserRepositoryInterface as UserRepository;
use App\Models\Schedule;
use App\Services\Supports\WithRepositoryTrait;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ScheduleSharedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\ActionType;
use App\Services\Supports\NotifySupport;
use App\Services\Supports\StrSupportTrait;
use App\Jobs\Supports\JobSupport;
use App\Jobs\ScheduleShareActivityJob;

class ScheduleService extends Service
{
  use WithRepositoryTrait, StrSupportTrait;
  /**
   * @var UserRepository
   */
  private $userRepository;
  private $jobSupport;

  /**
   * UserService constructor.
   * @param Repository $repository
   * @param Query $query,
   * @param UserRepository $userRepository
   * @param JobSupport $jobSupport
   * @param ScheduleShareActivityJob $job
   */
  public function __construct(
    Repository $repository,
    Query $query,
    UserRepository $userRepository,
    JobSupport $jobSupport,
    ScheduleShareActivityJob $job
  ) {
    $this->setRepository($repository);
    $this->setQuery($query);
    $this->userRepository = $userRepository;
    $this->jobSupport = $jobSupport;
    $this->jobSupport->init($job, 'schedule_shared');
  }

  public function index(array $params, ?array $relation = null)
  {
    return $this->query()->daily($params, $relation ?? $this->query()->relation());
  }

  /**
   * @param $ownerId
   * @return array
   */
  public function findByOwner($ownerId): array
  {
    $owner = $this->userRepository->find($ownerId);
    $schedules = $owner->sharedSchedules->load(['sharedMembers', 'createdBy']);
    return $schedules->map(function ($schedule) {
      if (!$schedule->is_show) {
        return [
          'id' => $schedule->id,
          'title' => Schedule::PRIVATE_TITLE,
          'is_public' => $schedule->is_public,
          'start' => $schedule->start,
          'end' => $schedule->end,
          'is_show' => $schedule->is_show,
          'color' => Schedule::PRIVATE_COLOR,
        ];
      }
      return $schedule;
    })->all();
  }

  /**
   * @param array $params
   * @return Schedule
   */
  public function store(array $params): Schedule
  {
    $params = $this->addMe($params);
    $newSchedule = $this->repository()->saveWithMembers($params, 'sharedMembers');

    Notification::send($newSchedule->sharedMembers->filter(function ($member) use ($newSchedule) {
      return NotifySupport::shouldSend($member, $newSchedule->created_by, ActionType::SCHEDULE_SHARED_KEY);
    }), new ScheduleSharedNotification($newSchedule));
    $this->jobSupport->dispatch($newSchedule);

    return $newSchedule;
  }

  /**
   * @param $id
   * @param array $loads
   * @return Schedule
   */
  public function find($id, ?array $loads = null): Schedule
  {
    return $this->repository()->find($id, $loads ?? $this->query()->relation());
  }

  /**
   * @param array $params
   * @param $id
   * @return Schedule
   */
  public function update(array $params, $id): Schedule
  {
    return $this->repository()->saveWithMembers($params, 'sharedMembers', $id);
  }

  /**
   * @param $id
   * @return Schedule
   */
  public function delete($id): Schedule
  {
    return $this->repository()->delete($id);
  }

  /**
   * @param array $params
   * @return array
   */
  private function addMe(array $params)
  {
    $params['sharedMembers'][Auth::user()->id] = [
      'is_editable' => 1,
      'shared_by' => Auth::user()->id,
    ];
    return $params;
  }
}
