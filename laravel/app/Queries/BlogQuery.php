<?php

namespace App\Queries;

use App\Contracts\Queries\BlogQueryInterface;
use App\Models\Blog;
use App\Queries\Abstracts\EloquentQuery;

class BlogQuery extends EloquentQuery implements BlogQueryInterface
{
  public function __construct(Blog $model)
  {
    $this->setBuilder($model);
    $this->setColumns(['title', 'body']);
    $this->setRelationTargets([
      'tags' => [
        'name'
      ],
      'comments' => [
        'body',
      ],
      'createdBy' => [
        'name',
      ],
    ]);
  }
}
