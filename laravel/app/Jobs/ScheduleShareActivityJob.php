<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ActionType;
use App\Models\Schedule;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;
use App\Contracts\Repositories\ActivityRepositoryInterface as ActivityRepository;
use App\Services\Supports\StrSupportTrait;

class ScheduleShareActivityJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  use StrSupportTrait;
  /**
   * @var Schedule
   */
  private $schedule;
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
  public function __construct(Schedule $schedule, ActionTypeRepository $actionTypeRepository, ActivityRepository $activityRepository)
  {
    $this->schedule = $schedule;
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
    $actionType = $this->actionTypeRepository->findBy('key', ActionType::SCHEDULE_SHARED_KEY);
    $content = $this->replaceAttribute($actionType[0]->template_message, [
      'from' => $this->schedule->createdBy->full_name,
      'body' => \Str::limit($this->schedule->title, 15, '（...）'),
    ]);

    $this->schedule->sharedMembers->each(function ($member) use ($actionType, $content) {
      if ($this->schedule->created_by !== $member->id) {
        $this->activityRepository->save([
          'action_type_id' => $actionType[0]->id,
          'model_id' => $this->schedule->id,
          'user_id' => $member->id,
          'content' => $content,
        ]);
      }
    });
  }
}
