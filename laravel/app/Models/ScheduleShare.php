<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ScheduleShare
 *
 * @property int $id
 * @property int $schedule_id スケジュールID
 * @property int $shared_with 共有相手
 * @property int $shared_by 共有した人
 * @property int $is_editable 編集権限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $sharedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare whereIsEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare whereSharedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare whereSharedWith($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleShare whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScheduleShare extends Model
{
  protected $table = 'schedule_shares';
  protected $fillable = [
    'shared_by',
    'is_editable',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function sharedBy()
  {
    return $this->belongsTo(User::class, 'shared_by', 'id');
  }
}
