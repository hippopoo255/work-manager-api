<?php

namespace App\Services;

use App\Contracts\Queries\ActivityQueryInterface as Query;
use App\Contracts\Repositories\ActivityRepositoryInterface as Repository;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;
use App\Contracts\Repositories\UserRepositoryInterface as UserRepository;
use App\Contracts\Repositories\AdminRepositoryInterface as AdminRepository;
use App\Services\Supports\WithRepositoryTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Admin;
use App\Models\ActionType;

class ActivityService extends Service
{
  use WithRepositoryTrait;

  private $actionTypeRepository;
  private $userRepository;
  private $adminRepository;

  /**
   * UserService constructor.
   * @param Repository $repository
   * @param ActionTypeRepository $actionTypeRepository
   * @param UserRepository $userRepository
   * @param AdminRepository $adminRepository
   * @param Query $query
   */
  public function __construct(
    Repository $repository,
    ActionTypeRepository $actionTypeRepository,
    UserRepository $userRepository,
    AdminRepository $adminRepository,
    Query $query
  ) {
    $this->setRepository($repository);
    $this->actionTypeRepository = $actionTypeRepository;
    $this->userRepository = $userRepository;
    $this->adminRepository = $adminRepository;
    $this->setQuery($query);
  }

  /**
   * Undocumented function
   *
   * @param integer|null $userId
   * @return array
   */
  public function findByUser(?int $userId = null): array
  {
    if (is_null($userId)) {
      $userId = Auth::user()->id;
    }
    $actionTypeIds = $this->actionTypeRepository->idsByAuthenticatable();
    return $this->query()->maxRecordsForUser(10, [
      'user_id' => $userId,
      'action_type_ids' => $actionTypeIds,
    ]);
  }

  public function read($userId)
  {
    if ($userId instanceof Model) {
      $userId = $userId->id;
    }

    return $this->repository()->read($userId);
  }

  /**
   * @param array $params
   * @param User|int $userId
   * @return array
   */
  public function findByCreatedUser(array $params, $userId)
  {
    if ($userId instanceof User) {
      $createdBy = $userId;
    } else {
      $createdBy = $this->userRepository->find($userId);
    }
    $actionTypeIds = $this->actionTypeRepository->idsByAuthenticatable($params['authenticatable']);
    $activities = $this->query()->findByCreatedUserAndActionType([
      'created_by' => $createdBy->id,
      'action_type_ids' => $actionTypeIds,
      // 'offset' => $params['offset'],
    ]);

    if ($params['authenticatable'] === 'user') {
      $groupedByActionType = collect($activities)->groupBy('action_type_id');
      $groupedByModelId = $groupedByActionType->flatMap(function ($activities) {
        $grouped = $activities->groupBy('model_id');
        return $grouped->map(function ($activities) {
          return $activities->first();
        });
      });
      return [
        'activities' => $groupedByModelId->forPage($params['page'], 10)->values()->all(),
        'hasMore' => ceil($groupedByModelId->count() / 10) != $params['page']
      ];
    } else {
      return [
        'activities' => $activities->forPage($params['page'], 10)->values()->all(),
        'hasMore' => ceil($activities->count() / 10) != $params['page']
      ];
    }
  }
}
