<?php

namespace App\Models;

use App\Contracts\Models\ModelInterface;
use App\Models\Abstracts\CommonModel as Model;

/**
 * App\Models\MeetingRecordPin
 *
 * @property int $id
 * @property int $user_id ピン留めしたユーザID
 * @property int $meeting_record_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin whereMeetingRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingRecordPin whereUserId($value)
 * @mixin \Eloquent
 */
class MeetingRecordPin extends Model implements ModelInterface
{
  protected $table = 'meeting_record_pins';
  protected $fillable = [
    'user_id',
    'meeting_record_id',
  ];
}
