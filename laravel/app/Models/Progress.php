<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;

/**
 * App\Models\Progress
 *
 * @property int $id
 * @property string $name 「未着手」など
 * @property int $value 達成値（数値が大きいほど完成度が高い）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|Progress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Progress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Progress query()
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereValue($value)
 * @mixin \Eloquent
 * @property int $created_by 作成者
 * @property int|null $updated_by 更新者
 * @property int|null $deleted_by 削除者
 * @property-read \App\Models\User $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Progress whereUpdatedBy($value)
 */
class Progress extends Model
{
  protected $table = 'progress';

  protected $fillable = [
    'name',
    'value',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  const COMPLETE_VALUE = 128;
  const EXCEPT_VALUE = self::COMPLETE_VALUE;

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function tasks()
  {
    return $this->hasMany(Task::class, 'progress_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
