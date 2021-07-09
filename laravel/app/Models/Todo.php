<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Todo
 *
 * @property int $id
 * @property int|null $meeting_record_id 議事録ID
 * @property int $owner_id 担当者
 * @property int $created_by 作成者
 * @property int|null $priority_id 優先順位ID
 * @property int|null $progress_id 進捗度ID
 * @property string $body 内容
 * @property int $time_limit 期日
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\MeetingRecord|null $meeting
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\Priority|null $priority
 * @property-read \App\Models\Progress|null $progress
 * @method static \Illuminate\Database\Eloquent\Builder|Todo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Todo newQuery()
 * @method static \Illuminate\Database\Query\Builder|Todo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Todo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereMeetingRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereProgressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereTimeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Todo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Todo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Todo withoutTrashed()
 * @mixin \Eloquent
 */
class Todo extends Model
{
  use SoftDeletes;

  protected $table = 'todos';
  protected $fillable = [
    'meeting_record_id',
    'owner_id',
    'created_by',
    'priority_id',
    'body',
    'time_limit',
  ];

  protected $casts = [
    'time_limit' => 'timestamp',
  ];
  protected $dates = [
    'time_limit',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function meeting()
  {
    return $this->belongsTo(MeetingRecord::class, 'meeting_record_id', 'id');
  }
  /**
   * Todoの担当者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function owner()
  {
    return $this->belongsTo(User::class, 'owner_id', 'id');
  }
  /**
   * 議事録作成者に登録されたTodo
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function priority()
  {
    return $this->belongsTo(Priority::class, 'priority_id', 'id');
  }
  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function progress()
  {
    return $this->belongsTo(Progress::class, 'progress_id', 'id');
  }
}
