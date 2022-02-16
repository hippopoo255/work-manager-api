<?php

namespace App\Queries;

use App\Contracts\Queries\PrefectureQueryInterface;
use App\Models\Prefecture;
use App\Queries\Abstracts\EloquentQuery;

class PrefectureQuery extends EloquentQuery implements PrefectureQueryInterface
{
  public function __construct(Prefecture $model)
  {
    $this->setBuilder($model);
    $this->setColumns([]);
    $this->setRelationTargets([]);
  }
}
