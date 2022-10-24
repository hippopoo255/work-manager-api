<?php

namespace App\Services;

use App\Contracts\Queries\OrganizationQueryInterface as Query;
use App\Contracts\Repositories\OrganizationRepositoryInterface as Repository;
use App\Contracts\Repositories\UserRepositoryInterface as UserRepository;
use App\Services\Supports\RepositoryUsingSupport;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrganizationCreatedNotification;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Jobs\MasterInitializationJob;

class OrganizationService extends Service
{
  use RepositoryUsingSupport;

  private $userRepository;
  /**
   * UserService constructor.
   * @param Repository $repository
   * @param Query $query
   * @param UserRepository $userRepository
   */
  public function __construct(
    Repository $repository,
    Query $query,
    UserRepository $userRepository
  ) {
    $this->setRepository($repository);
    $this->setQuery($query);
    $this->userRepository = $userRepository;
  }

  /**
   * @param array $params
   * @return User
   */
  public function store(array $params): User
  {
    $params['supervisor_id'] = Auth::user()->id;
    $organization = $this->repository()->save($params);

    if ($organization) {
      // リクエストユーザのorganization_idを更新し、以後エンドポイントにアクセスできるようにする
      $user = $this->userRepository->update([
        'organization_id' => $organization->id,
      ], Auth::user()->id);

      $user = $user->makeVisible(['login_id']);

      // メール通知「組織情報登録完了のお知らせ」
      Notification::send([$user], new OrganizationCreatedNotification());
      MasterInitializationJob::dispatch($user)->onQueue('default');
      return $user;
    }

    return Auth::user();
  }

  /**
   * @param array $params
   * @param $id
   * @return Organization
   */
  public function update(array $params, $id): Organization
  {
    return $this->repository()->save($params, $id);
  }

  /**
   * @param $id
   * @return Organization
   */
  public function delete($id): Organization
  {
    return $this->repository()->delete($id);
  }
}
