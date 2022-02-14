<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ActionType;
use App\Models\ChatMessage;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;
use App\Contracts\Repositories\ActivityRepositoryInterface as ActivityRepository;
use App\Services\Supports\StrSupportTrait;

class MessageSentActivityJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  use StrSupportTrait;

  private $chatMessage;
  private $actionTypeRepository;
  private $activityRepository;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(ChatMessage $chatMessage, ActionTypeRepository $actionTypeRepository, ActivityRepository $activityRepository)
  {
    $this->chatMessage = $chatMessage;
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
    $actionType = $this->actionTypeRepository->findBy('key', ActionType::MESSAGE_SENT_KEY);
    $content = $this->replaceAttribute($actionType[0]->template_message, [
      'from' => $this->chatMessage->createdBy->full_name,
      'body' => \Str::limit($this->chatMessage->body, 15, '（...）'),
    ]);

    $this->chatMessage->chatRoom->members->each(function ($member) use ($actionType, $content) {
      if ($this->chatMessage->created_by !== $member->id) {
        $this->activityRepository->save([
          'action_type_id' => $actionType[0]->id,
          'model_id' => $this->chatMessage->chatRoom->id,
          'user_id' => $member->id,
          'content' => $content,
        ]);
      }
    });
  }
}
