<?php

namespace App\Repositories\Abstracts;

use App\Repositories\Traits\ModelTrait as ModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

abstract class EloquentRepository extends CommonAbstractRepository
{
  use ModelTrait;

  public function qualifiedStoreParams(array $params): array
  {
    $params['updated_by'] = Auth::check() ? Auth::user()->id : null;
    return parent::qualifiedStoreParams($params);
  }

  public function qualifiedUpdateParams(array $params): array
  {
    $params['updated_by'] = Auth::check() ? Auth::user()->id : null;
    return parent::qualifiedUpdateParams($params);
  }

  /**
   * @return Model|null
   */
  public function save(array $params, $id = null): Model
  {
    if (is_null($id)) {
      $model = $this->store($this->qualifiedStoreParams($this->qualifiedParams($params)));
    } else {
      $model = $this->update($this->qualifiedUpdateParams($this->qualifiedParams($params)), $id);
    }
    return $model;
  }

  /**
   * @param array $params
   * @param $id
   * @return Model
   */
  public function update(array $params, $id): Model
  {
    $model = $this->find($id);
    if ($model) {
      $model->update($params);
    }
    return $model;
  }

  /**
   * @param array $params
   * @return Model
   */
  public function store(array $params): Model
  {
    return $this->model()->create($params);
  }

  /**
   * @return Model
   */
  public function delete($id): Model
  {
    $model = $this->find($id);
    if ($model) {
      $model->customDelete();
    }
    return $model;
  }

  /**
   * @param array $params
   * @param Model $parent
   * @param string $method
   * @param $id
   * @return Model
   */
  public function attach(array $params, Model $parent, string $method, $id = null): Model
  {
    if ($id instanceof Model) {
      $id = $id->id;
    }
    if (method_exists($parent, $method)) {
      // 子モデル
      return $parent->{$method}()->updateOrCreate(
        ['id' => $id],
        $this->qualifiedUpdateParams($params)
      );
    }
    // リレーション定義が無かった場合は親モデルを返す
    return $parent;
  }

  /**
   * @param array $params
   * @param string $method
   * @param $id
   * @return Model
   */
  public function saveWithMembers(array $params, string $method = 'members', $id = null): Model
  {
    $model = $this->save($params, $id);
    if (method_exists($model, $method)) {
      $model->{$method}()->sync($params[$method]);
    }
    return $model;
  }

  /**
   * @param $id
   * @param array|null $loads
   * @return Model
   */
  public function find($id, ?array $loads = []): Model
  {
    if ($id instanceof Model) {
      $model = $id;
    } else {
      $model = $this->model()->findOrFail($id);
    }
    if ($loads && count($loads)) {
      $model->load($loads);
    }
    return $model;
  }

  /**
   * @return Collection
   */
  public function all(): Collection
  {
    return $this->model()->all();
  }

  /**
   * @param $columnName
   * @param $value
   * @return Model[]
   */
  public function findBy($columnName, $value, array $loads = []): array
  {
    return $this->model()->where($columnName, $value)->get()->load($loads)->all();
  }
}
