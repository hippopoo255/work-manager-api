<?php

namespace App\Models;

use App\Contracts\Models\RelationalDeleteInterface;
use App\Models\Abstracts\CommonModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\MeetingDecision
 *
 * @property int $id
 * @property int $meeting_record_id 議事録ID
 * @property int|null $decided_by 決定者
 * @property int $created_by 入力者
 * @property string|null $subject 議題
 * @property string $body 決定内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $decidedBy
 * @property-read \App\Models\MeetingRecord $meetingRecord
 * @property-read \App\Models\User $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision newQuery()
 * @method static \Illuminate\Database\Query\Builder|MeetingDecision onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereDecidedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereMeetingRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereCreatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|MeetingDecision withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MeetingDecision withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 */
class MeetingDecision extends CommonModel implements RelationalDeleteInterface
{
  use SoftDeletes;

  protected $table = 'meeting_decisions';

  protected $fillable = [
    'meeting_record_id',
    'decided_by',
    'created_by',
    'subject',
    'body',
  ];

  const RELATIONS_ARRAY = [
    'tasks',
    'decidedBy',
    'createdBy',
  ];

  /**
   * 議事録登録者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
  /**
   * 決定者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function decidedBy()
  {
    return $this->belongsTo(User::class, 'decided_by', 'id');
  }
  /**
   * 決定者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function meetingRecord()
  {
    return $this->belongsTo(MeetingRecord::class, 'meeting_record_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function tasks()
  {
    return $this->hasMany(Task::class, 'meeting_decision_id', 'id');
  }

  public function getDeleteRelations(): array
  {
    return [
      $this->tasks
    ];
  }
}
