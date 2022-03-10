<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Progress;
use App\Models\ActionType;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;
use App\Contracts\Repositories\ActivityRepositoryInterface as ActivityRepository;

class ProgressCreateActivityJob extends ActivityJob implements ShouldQueue
{
  /**
   * @var Progress
   */
  protected $model;
  protected $bodyLength = 30;

  /**
   * @param Progress $model
   * @param ActionTypeRepository $actionTypeRepository
   * @param ActivityRepository $activityRepository
   */
  public function __construct(Progress $model, ActionTypeRepository $actionTypeRepository, ActivityRepository $activityRepository)
  {
    parent::__construct($actionTypeRepository, $activityRepository);
    $this->model = $model;
    $this->actionTypeKey = ActionType::PROGRESS_CREATE_KEY;
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
