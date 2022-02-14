<?php

namespace App\Models;

use App\Contracts\Models\ModelInterface;
use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property int $created_by 予定作成者
 * @property string $title 予定の内容
 * @property int $start 開始日時
 * @property int $end 終了日時
 * @property bool $is_public 公開設定
 * @property string|null $color カラー
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $createdBy
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
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Schedule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Schedule withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $memo メモ
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereMemo($value)
 * @property-read bool $can_edit
 * @property-read mixed $is_show
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereTitle($value)
 */
class Schedule extends Model implements ModelInterface
{
  use SoftDeletes;
  const PRIVATE_COLOR = '#cccccc';
  const PRIVATE_TITLE = '非公開予定';

  protected $table = 'schedules';
  protected $fillable = [
    'created_by',
    'title',
    'start',
    'end',
    'is_public',
    'color',
    'memo',
  ];

  protected $casts = [
    'is_public' => 'boolean',
    'start' => 'datetime',
    'end' => 'datetime',
  ];

  protected $dates = [
    'start',
    'end',
  ];

  const RELATIONS_ARRAY = [
    'sharedMembers',
    'createdBy',
  ];

  protected $appends = [
    'can_edit',
    'is_show',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function sharedMembers()
  {
    return $this->belongsToMany(User::class, 'schedule_shares', 'schedule_id', 'shared_with')
      ->as('option')
      ->withTimestamps()
      ->withPivot('is_editable', 'shared_by');
  }

  /**
   * @return bool
   */
  public function getCanEditAttribute(): bool
  {
    return $this->sharedMembers->contains(function ($member) {
      if (Auth::guest()) {
        return false;
      }
      return Auth::user()->id === $member->id && $member->option->is_editable;
    });
  }

  public function getIsShowAttribute()
  {
    if (!Auth::check()) {
      return false;
    }
    return $this->is_public || $this->sharedMembers->contains(function ($member) {
      return $member->id === Auth::user()->id;
    });
  }
}
