<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\MeetingPlace
 *
 * @property int $id
 * @property string $name 開催場所
 * @property int|null $created_by 登録者
 * @property int|null $updated_by 更新者
 * @property int|null $deleted_by 削除者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeetingRecord[] $meetings
 * @property-read int|null $meetings_count
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace newQuery()
 * @method static \Illuminate\Database\Query\Builder|MeetingPlace onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingPlace whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|MeetingPlace withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MeetingPlace withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\User|null $createdBy
 */
class MeetingPlace extends Model
{
  protected $table = 'meeting_places';
  protected $fillable = [
    'name',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function meetings()
  {
    return $this->hasMany(MeetingRecord::class, 'meeting_place_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
