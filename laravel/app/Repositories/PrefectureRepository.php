<?php

namespace App\Repositories;

use App\Contracts\Repositories\PrefectureRepositoryInterface;
use App\Models\Prefecture;
use App\Repositories\Abstracts\EloquentRepository;

class PrefectureRepository extends EloquentRepository implements PrefectureRepositoryInterface
{
  public function __construct(Prefecture $model)
  {
    $this->setModel($model);
  }
}
