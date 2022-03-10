<?php

namespace App\Repositories;

use App\Contracts\Repositories\ActionTypeRepositoryInterface;
use App\Models\ActionType;
use App\Repositories\Abstracts\EloquentRepository;

class ActionTypeRepository extends EloquentRepository implements ActionTypeRepositoryInterface
{
  public function __construct(ActionType $model)
  {
    $this->setModel($model);
  }

  /**
   * Undocumented function
   *
   * @param string $columnName
   * @param $val
   * @return int
   */
  public function findIdBy($val, string $columnName = 'key'): int
  {
    $model = $this->findBy($columnName, $val);
    return $model[0]->id;
  }

  /**
   * @param string $authenticatable
   * @return array
   */
  public function idsByAuthenticatable($authenticatable = 'user')
  {
    $actionTypeKeys = $authenticatable === 'user' ? ActionType::USER_ACTION_TYPES : ActionType::ADMIN_ACTION_TYPES;
    return $this->model()->whereIn('key', $actionTypeKeys)->pluck('id')->toArray();
  }
}
