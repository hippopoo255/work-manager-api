<?php

namespace App\Services;

use App\Contracts\Queries\PrefectureQueryInterface as Query;
use App\Contracts\Repositories\PrefectureRepositoryInterface as Repository;
use App\Services\Supports\WithRepositoryTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class PrefectureService extends Service
{
  use WithRepositoryTrait;

  /**
   * UserService constructor.
   * @param Repository $repository
   * @param Query $query
   */
  public function __construct(
    Repository $repository,
    Query $query
  ) {
    $this->setRepository($repository);
    $this->setQuery($query);
    // else repository...
  }

  /**
   * @return Collection
   */
  public function index(): Collection
  {
    return $this->repository()->all();
  }
}
