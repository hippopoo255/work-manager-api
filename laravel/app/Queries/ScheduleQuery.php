<?php

namespace App\Queries;

use App\Contracts\Queries\ScheduleQueryInterface;
use App\Models\Schedule;
use App\Queries\Abstracts\EloquentQuery;
use Carbon\Carbon;

class ScheduleQuery extends EloquentQuery implements ScheduleQueryInterface
{
  public function __construct(Schedule $model)
  {
    $this->setBuilder($model);
    $this->setColumns(['scheduled_by', 'content', 'start_date', 'end_date', 'color']);
    $this->setRelationTargets([
      'sharedMembers' => [
        'family_name',
        'given_name',
        'department_code',
      ],
    ]);
  }

  public function daily($params, $relation)
  {
    $query = parent::search($params, $relation);
    $tomorrow = Carbon::tomorrow()->format('Y-m-d H:i:s');
    $today = Carbon::today()->format('Y-m-d H:i:s');
    $query->where('start_date', '<', $tomorrow)
    ->where('end_date', '>=', $today);
    return $query->get()->all();
  }


}