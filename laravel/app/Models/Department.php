<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Department
 *
 * @property int $id
 * @property string $department_id 部署コード
 * @property string $name 部署名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $members
 * @property-read int|null $members_count
 * @method static \Illuminate\Database\Eloquent\Builder|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department newQuery()
 * @method static \Illuminate\Database\Query\Builder|Department onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereDepartmentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Department withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Department withoutTrashed()
 * @mixin \Eloquent
 */
class Department extends Model
{
  use SoftDeletes;

  protected $table = 'departments';

  protected $fillable = [
    'name',
    'department_code',
    'color',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function members()
  {
    return $this->hasMany(User::class, 'department_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
