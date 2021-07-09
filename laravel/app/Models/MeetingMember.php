<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MeetingMember
 *
 * @property int $id
 * @property int $member_id 参加者ID
 * @property int $meeting_record_id 議事録ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember whereMeetingRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeetingMember whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MeetingMember extends Model
{
    //
}
