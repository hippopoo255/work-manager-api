<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\MeetingRecord
 *
 * @property int $id
 * @property int $recorded_by 議事録作成者
 * @property int|null $place_id 開催場所
 * @property \Illuminate\Support\Carbon $meeting_date 開催日
 * @property string $title 会議名
 * @property string|null $summary ミーティング概要
 * @property int|null $updated_by 議事録更新者
 * @property int|null $deleted_by 議事録削除者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $members
 * @property-read int|null $members_count
 * @property-read \App\Models\MeetingPlace|null $place
 * @property-read \App\Models\User $recordedBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Todo[] $todos
 * @property-read int|null $todos_count
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord newQuery()
 * @method static \Illuminate\Database\Query\Builder|MeetingRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereMeetingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord wherePlaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereRecordedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|MeetingRecord withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MeetingRecord withoutTrashed()
 * @mixin \Eloquent
 */
class MeetingRecord extends Model
{
  use SoftDeletes;

  protected $table = 'meeting_records';

  protected $fillable = [
    'recorded_by',
    'updated_by',
    'deleted_by',
    'place_id',
    'meeting_date',
    'title',
    'summary',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'meeting_date' => 'datetime',
  ];

  protected $dates = [
    'meeting_date'
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function recordedBy()
  {
    return $this->belongsTo(User::class, 'recorded_by', 'id');
  }
  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function place()
  {
    return $this->belongsTo(MeetingPlace::class, 'place_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function todos()
  {
    return $this->hasMany(Todo::class, 'meeting_record_id', 'id');
  }

  /**
   * ミーティング参加者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function members()
  {
    return $this->belongsToMany(User::class, 'meeting_members', 'meeting_record_id', 'member_id')->withTimestamps();
  }
}
