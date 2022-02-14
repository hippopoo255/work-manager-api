<?php

namespace App\Queries\Abstracts;

use App\Queries\Traits\BuilderTrait;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class EloquentQuery extends CommonAbstractQuery
{
  use BuilderTrait;

  private $targetColumns = [];
  private $relationTargets = [];

  /**
   * @param array $columns
   */
  protected function setColumns(array $columns)
  {
    $this->targetColumns = $columns;
  }
  /**
   * @param array $relationTargets
   */
  protected function setRelationTargets(array $relationTargets)
  {
    $this->relationTargets = $relationTargets;
  }

  /**
   * @return array
   */
  public function columns()
  {
    return $this->targetColumns;
  }

  /**
   * @return array
   */
  public function relationTargets()
  {
    return $this->relationTargets;
  }

  /**
   * @param array $params
   * @param array|null $relation
   * @return array
   */
  public function all(array $params, ?array $relation = null)
  {
    return $this->search($params, $relation ?? $this->relation())->get()->all();
  }

  /**
   * @param int $limit
   * @param array $params
   * @param array|null $relation
   * @return array
   */
  public function getLimit(int $limit, array $params, ?array $relation = null): array
  {
    return $this->search($params, $relation ?? $this->relation())->limit($limit)->get()->all();
  }

  /**
   * @param array $params
   * @param int|null $perPage
   * @param array|null $relation
   * @return LengthAwarePaginator
   */
  public function paginate(array $params, ?int $perPage = null, ?array $relation = null): LengthAwarePaginator
  {
    return $this->search($params, $relation ?? $this->relation())->paginate($perPage ?? $this->perPage());
  }

  /**
   * @param array $params
   * @param array $relation
   * @return Builder
   */
  public function search(array $params, array $relation = []): Builder
  {
    $query = $this->builder()->with($relation);
    $likely = isset($params['likely']) ? (bool) $params['likely'] : true;

    foreach ($params as $column => $val) {
      if ($column === 'keyword') {
        $query = $this->searchByKeywords($query, $val);
      } elseif (count($this->splitColumn($column)) >= 2) {
        $query = $this->searchOtherLocation($query, [$column => $val]);
      } elseif ($this->isValidColumnName($column)) {
        $operator = $likely ? 'like' : '=';
        $value = $likely ? $this->liked($val) : $val;
        $query->where($column, $operator, $value);
      }
    }

    if (isset($params['sort_key'])) {
      $query = $this->order($params, $query);
    } else {
      $query->orderBy('id', 'desc');
    }
    return $query;
  }

  /**
   * @param array $params
   * @param Builder $query
   */
  public function order(array $params, Builder $query): Builder
  {
    if ($this->isValidColumnName($params['sort_key'])) {
      return $query->orderBy($params['sort_key'], $params['order_by'] ?? 'desc');
    }
  }

  /**
   * @param Builder $query
   * @param string $keyword
   * @return Builder
   */
  protected function searchByKeywords(Builder $query, string $keyword)
  {
    $likeVal = $this->liked($keyword);
    $query->where(function ($query) use ($likeVal) {
      foreach ($this->targetColumns as $columnName) {
        $query->orWhere($columnName, 'like', $likeVal);
      }
      foreach ($this->relationTargets as $relation => $relationColumns) {
        foreach ($relationColumns as $c) {
          $query->orWhereHas($relation, function ($q) use ($likeVal, $c) {
            $q->where($c, 'like', $likeVal);
          });
        }
      }
    });
    return $query;
  }

  /**
   * @param Builder $query
   * @param array $param
   */
  protected function searchOtherLocation(Builder $query, array $param)
  {
    // ['table:column' => 1]
    $queryParamKey = key($param);
    $queryValue = $param[$queryParamKey];
    $relationalPair = $this->splitColumn($queryParamKey);
    $query = $this->otherLocationQuery($query, $relationalPair[0], $relationalPair[1], $queryValue, '=');

    return $query;
  }

  /**
   * @param Builder $query
   * @param string $tableName
   * @param string $columnName
   * @param string $value
   * @return Builder
   */
  protected function otherLocationQuery(Builder $query, string $tableName, string $columnName, string $value, string $filter = '=')
  {
    if ($filter === 'like') {
      $value = $this->liked($value);
    }
    $query->whereHas($tableName, function ($q) use ($tableName, $columnName, $value, $filter) {
      if ($this->includesInRelationalColumns($tableName, $columnName)) {
        $q->where($columnName, $filter, $value);
      }
    });
    return $query;
  }
}
