<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MeetingPlace;
use App\Models\ActionType;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;
use App\Contracts\Repositories\ActivityRepositoryInterface as ActivityRepository;

class MeetingPlaceCreateActivityJob extends ActivityJob implements ShouldQueue
{
  /**
   * @var MeetingPlace
   */
  protected $model;
  protected $bodyLength = 30;

  /**
   * @param MeetingPlace $model
   * @param ActionTypeRepository $actionTypeRepository
   * @param ActivityRepository $activityRepository
   */
  public function __construct(MeetingPlace $model, ActionTypeRepository $actionTypeRepository, ActivityRepository $activityRepository)
  {
    parent::__construct($actionTypeRepository, $activityRepository);
    $this->model = $model;
    $this->actionTypeKey = ActionType::MEETING_PLACE_CREATE_KEY;
    $this->bodyKey = 'name';
  }

  protected function store(array $actionType, string $content)
  {
    $attributes = [
      'action_type_id' => $actionType[0]->id,
      'model_id' => $this->model->id,
      'created_by' => $this->model->created_by,
      'content' => $content,
    ];
    $this->activityRepository->save($attributes);
  }
}
