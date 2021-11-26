<?php

namespace App\Models;

use App\Contracts\Models\ModelInterface;
use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property int|null $meeting_decision_id 会議決定事項ID
 * @property int $owner_id 担当者
 * @property int $created_by 作成者
 * @property int|null $priority_id 優先順位ID
 * @property int|null $progress_id 進捗度ID
 * @property string $body 内容
 * @property \Illuminate\Support\Carbon $time_limit 期日
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\MeetingRecord|null $meeting
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\Priority|null $priority
 * @property-read \App\Models\Progress|null $progress
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereMeetingRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereProgressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTimeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Task withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\MeetingDecision|null $meetingDecision
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereMeetingDecisionId($value)
 * @property-read bool $is_busy
 * @property-read bool $is_except
 * @property-read bool $is_over
 * @property-read bool $is_warning
 * @property-read string $status
 * @property-read bool $too_over
 * @property-read bool $is_clear
 */
class Task extends Model implements ModelInterface
{
  use SoftDeletes;

  protected $table = 'tasks';
  protected $fillable = [
    'meeting_record_id',
    'owner_id',
    'created_by',
    'priority_id',
    'progress_id',
    'body',
    'time_limit',
  ];

  protected $appends = [
    'status'
  ];
  protected $casts = [
    'time_limit' => 'datetime',
  ];

  const RELATIONS_ARRAY = [
    'createdBy', 'owner', 'priority', 'progress',
  ];

  const TOO_OVER_DAY = 7;
  const WARNING_LIMIT_DAY = 3;
  /**
   * ちょうど"WARNING_LIMIT_DAY"日後に迫っているタスクをリマインドに含める -> 1
   * 含めない -> 0
   */
  const INCLUDES_JUST_DATE = 1;

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function meetingDecision()
  {
    return $this->belongsTo(MeetingDecision::class, 'meeting_decision_id', 'id');
  }
  /**
   * Taskの担当者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function owner()
  {
    return $this->belongsTo(User::class, 'owner_id', 'id');
  }
  /**
   * 議事録作成者に登録されたTask
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

  /**
   * @return boolean
   */
  public function getIsWarningAttribute(): bool
  {
    $future = Carbon::today()->addDays(self::WARNING_LIMIT_DAY + self::INCLUDES_JUST_DATE);
    $timeLimit = Carbon::parse($this->time_limit);
    return $timeLimit->lt($future) && !$this->is_over;
  }

  /**
   * @return boolean
   */
  public function getIsOverAttribute(): bool
  {
    $timeLimit = Carbon::parse($this->time_limit);
    // Carbon::lt() = $timeLimitの日付が今日(Carbon::today())より前か判定する
    return $timeLimit->lt(Carbon::now());
  }

  /**
   * @return boolean
   */
  public function getTooOverAttribute(): bool
  {
    $timeLimit = Carbon::parse($this->time_limit);
    return $this->is_over && $timeLimit->diffInDays(Carbon::today()) >= self::TOO_OVER_DAY;
  }

  /**
   * @return boolean
   * 状態が「完了」以上 or 期日が7日以上経過している
   */
  public function getIsExceptAttribute(): bool
  {
    return $this->progress->value >= Progress::EXCEPT_VALUE || $this->too_over;
  }

  /**
   * @return boolean
   */
  public function getIsBusyAttribute(): bool
  {
    return !$this->is_except && ($this->is_warning || $this->is_over);
  }

  /**
   * @return string
   */
  public function getStatusAttribute(): string
  {
    if ($this->is_clear) {
      return 'clear';
    }
    if (!!$this->progress_id && $this->progress->value >= Progress::EXCEPT_VALUE) {
      return 'pending';
    }
    if ($this->is_over) {
      return 'over';
    }
    if ($this->is_warning) {
      return 'warning';
    }
    return 'safe';
  }

  /**
   * @return bool
   */
  public function getIsClearAttribute(): bool
  {
    if (!!$this->progress_id) {
      return $this->progress->value === Progress::COMPLETE_VALUE;
    }
    return false;
  }
}
