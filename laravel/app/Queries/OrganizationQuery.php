<?php

namespace App\Queries;

use App\Contracts\Queries\OrganizationQueryInterface;
use App\Models\Organization;
use App\Queries\Abstracts\EloquentQuery;

class OrganizationQuery extends EloquentQuery implements OrganizationQueryInterface
{
  public function __construct(Organization $model)
  {
    $this->setBuilder($model);
    $this->setColumns([]);
    $this->setRelationTargets([]);
  }
}
