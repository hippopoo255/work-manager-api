<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\MeetingDecision
 *
 * @property int $id
 * @property int $meeting_record_id 議事録ID
 * @property int|null $decided_by 決定者
 * @property int $written_by 入力者
 * @property string|null $subject 議題
 * @property string $body 決定内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $decidedBy
 * @property-read \App\Models\MeetingRecord $meetingRecord
 * @property-read \App\Models\User $writtenBy
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
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingDecision whereWrittenBy($value)
 * @method static \Illuminate\Database\Query\Builder|MeetingDecision withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MeetingDecision withoutTrashed()
 * @mixin \Eloquent
 */
class MeetingDecision extends Model
{
  use SoftDeletes;

  protected $table = 'meeting_decisions';

  protected $fillable = [
    'meeting_record_id',
    'decided_by',
    'written_by',
    'subject',
    'body',
  ];

  /**
   * 議事録登録者
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function writtenBy()
  {
    return $this->belongsTo(User::class, 'written_by', 'id');
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
}
