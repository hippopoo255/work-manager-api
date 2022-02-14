<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ActionType;
use App\Models\MeetingRecord;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;
use App\Contracts\Repositories\ActivityRepositoryInterface as ActivityRepository;
use App\Services\Supports\StrSupportTrait;

class MeetingRecordJoinedActivityJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  use StrSupportTrait;
  /**
   * @var MeetingRecord
   */
  private $meetingRecord;
  /**
   * @var ActionTypeRepository
   */
  private $actionTypeRepository;
  /**
   * @var ActivityRepository
   */
  private $activityRepository;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(MeetingRecord $meetingRecord, ActionTypeRepository $actionTypeRepository, ActivityRepository $activityRepository)
  {
    $this->meetingRecord = $meetingRecord;
    $this->actionTypeRepository = $actionTypeRepository;
    $this->activityRepository = $activityRepository;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $actionType = $this->actionTypeRepository->findBy('key', ActionType::MEETING_RECORD_JOINED_KEY);
    $content = $this->replaceAttribute($actionType[0]->template_message, [
      'from' => $this->meetingRecord->createdBy->full_name,
      'body' => \Str::limit($this->meetingRecord->title, 15, '（...）'),
    ]);

    $this->meetingRecord->members->each(function ($member) use ($actionType, $content) {
      if ($this->meetingRecord->created_by !== $member->id) {
        $this->activityRepository->save([
          'action_type_id' => $actionType[0]->id,
          'model_id' => $this->meetingRecord->id,
          'user_id' => $member->id,
          'content' => $content,
        ]);
      }
    });
  }
}
