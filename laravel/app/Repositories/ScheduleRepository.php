<?php

namespace App\Repositories;

use App\Contracts\Repositories\ScheduleRepositoryInterface;
use App\Models\Schedule;
use App\Repositories\Abstracts\EloquentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ScheduleRepository extends EloquentRepository implements ScheduleRepositoryInterface
{
  public function __construct(Schedule $model)
  {
    $this->setModel($model);
  }

  /**
   * @param array $params
   * @param string $method
   * @param null $id
   * @return Model
   */
  public function saveWithMembers(array $params, string $method = 'sharedMembers', $id = null): Model
  {
    if (is_null($id)) {
      $params['created_by'] = Auth::user()->id;
    }
    $schedule = parent::saveWithMembers($params, $method, $id);
    $schedule->load(Schedule::RELATIONS_ARRAY);
    return $schedule;
  }
}
