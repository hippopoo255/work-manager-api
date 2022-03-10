<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Department;
use App\Models\ActionType;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;
use App\Contracts\Repositories\ActivityRepositoryInterface as ActivityRepository;

class DepartmentCreateActivityJob extends ActivityJob implements ShouldQueue
{
  /**
   * @var Department
   */
  protected $model;
  protected $bodyLength = 30;

  /**
   * @param Department $model
   * @param ActionTypeRepository $actionTypeRepository
   * @param ActivityRepository $activityRepository
   */
  public function __construct(Department $model, ActionTypeRepository $actionTypeRepository, ActivityRepository $activityRepository)
  {
    parent::__construct($actionTypeRepository, $activityRepository);
    $this->model = $model;
    $this->actionTypeKey = ActionType::DEPARTMENT_CREATE_KEY;
    $this->bodyKey = ['department_code', 'name'];
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
