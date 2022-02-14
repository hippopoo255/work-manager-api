<?php

namespace App\Models;

use App\Contracts\Models\RelationalDeleteInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\MeetingRecord
 *
 * @property int $id
 * @property int $created_by 議事録作成者
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
 * @property-read \App\Models\User $createdBy
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
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|MeetingRecord withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MeetingRecord withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeetingDecision[] $decisions
 * @property-read int|null $decisions_count
 * @property int $role_id 権限ID
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecord whereRoleId($value)
 * @property-read bool $is_editable
 * @property-read bool $is_pin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $pinedUsers
 * @property-read int|null $pined_users_count
 */
class MeetingRecord extends Model implements RelationalDeleteInterface
{
  use SoftDeletes;

  const RELATIONS_ARRAY = [
    'role',
    'decisions',
    'place',
    'members',
    'createdBy',
  ];

  protected $table = 'meeting_records';

  protected $fillable = [
    'created_by',
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

  protected $appends = [
    'is_editable',
    'is_pin',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function role()
  {
    return $this->belongsTo(Role::class, 'role_id', 'id');
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
  public function decisions()
  {
    return $this->hasMany(MeetingDecision::class, 'meeting_record_id', 'id');
  }

  /**
   * ミーティング参加者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function members()
  {
    return $this->belongsToMany(User::class, 'meeting_members', 'meeting_record_id', 'member_id')->withTimestamps();
  }

  /**
   * ブックマークしているユーザー達
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function pinedUsers()
  {
    return $this->belongsToMany(User::class, 'meeting_record_pins', 'meeting_record_id', 'user_id')->withTimestamps();
  }

  /**
   * @return bool
   */
  public function getIsPinAttribute(): bool
  {
    if (Auth::check()) {
      return $this->pinedUsers->contains(function ($user) {
        return $user->id === Auth::user()->id;
      });
    }
    return false;
  }

  /**
   * @return bool
   */
  public function getIsEditableAttribute(): bool
  {
    if (Auth::check()) {
      return $this->created_by === Auth::user()->id;
    }
    return false;
  }

  public function getDeleteRelations(): array
  {
    return [
      $this->decisions
    ];
  }
}
