<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ChatMessage
 *
 * @property int $id
 * @property int $chat_room_id ルームID
 * @property int $written_by 投稿者
 * @property int|null $mentioned_to メンション相手
 * @property string $body メッセージ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ChatRoom $chatRoom
 * @property-read \App\Models\User $writtenBy
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereChatRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereMentionedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereWrittenBy($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessageImage[] $images
 * @property-read int|null $images_count
 */
class ChatMessage extends Model
{
  protected $table = 'chat_messages';

  protected $fillable = [
    'chat_room_id',
    'written_by',
    'mentioned_to',
    'body',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function writtenBy()
  {
    return $this->belongsTo(User::class, 'written_by', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function to()
  {
    return $this->belongsTo(User::class, 'mentioned_to', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function chatRoom()
  {
    return $this->belongsTo(ChatRoom::class, 'chat_room_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function images()
  {
    return $this->hasMany(ChatMessageImage::class, 'chat_message_id', 'id');
  }

}
