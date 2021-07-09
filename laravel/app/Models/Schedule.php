<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property int $scheduled_by 予定作成者
 * @property string $content 予定の内容
 * @property int $start_date 開始日時
 * @property int $end_date 終了日時
 * @property bool $is_public 公開設定
 * @property string|null $color カラー
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $scheduledBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $sharedMembers
 * @property-read int|null $shared_members_count
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Query\Builder|Schedule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereScheduledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Schedule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Schedule withoutTrashed()
 * @mixin \Eloquent
 */
class Schedule extends Model
{
  use SoftDeletes;

  protected $table = 'schedules';
  protected $fillable = [
    'scheduled_by',
    'content',
    'start_date',
    'end_date',
    'is_public',
    'color',
  ];

  protected $casts = [
    'is_public' => 'boolean',
    'start_date' => 'timestamp',
    'end_date' => 'timestamp',
  ];

  protected $dates = [
    'start_date',
    'end_date',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function scheduledBy()
  {
    return $this->belongsTo(User::class, 'scheduled_by', 'id');
  }
  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function sharedMembers()
  {
    return $this->belongsToMany(User::class, 'schedule_shares', 'schedule_id', 'shared_with')
      ->withTimestamps()
      ->withPivot('is_editable', 'shared_by');
  }
}
