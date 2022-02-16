<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrganizationRepositoryInterface;
use App\Models\Organization;
use App\Repositories\Abstracts\EloquentRepository;

class OrganizationRepository extends EloquentRepository implements OrganizationRepositoryInterface
{
  public function __construct(Organization $model)
  {
    $this->setModel($model);
  }
}
