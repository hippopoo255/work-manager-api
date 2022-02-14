<?php

namespace App\Repositories;

use App\Contracts\Repositories\BlogRepositoryInterface;
use App\Models\Blog;
use App\Repositories\Abstracts\EloquentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BlogRepository extends EloquentRepository implements BlogRepositoryInterface
{
  public function __construct(Blog $model)
  {
    $this->setModel($model);
  }

  /**
   * @return Model
   */
  public function saveWithTag(array $params, $id = null): Model
  {
    $params['created_by'] = Auth::user()->id;
    $blog = parent::save($params, $id);
    if (isset($params['tags'])) {
      $blog->tags()->sync($params['tags']);
    }
    return $blog;
  }
}
