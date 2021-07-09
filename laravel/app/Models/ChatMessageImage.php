<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChatMessageImage
 *
 * @property int $id
 * @property int $chat_message_id チャットID
 * @property string $file_path ファイルパス
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ChatMessage $chatMessage
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage whereChatMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChatMessageImage extends Model
{
  protected $table = 'chat_message_images';

  protected $fillable = [
    'chat_message_id',
    'file_path',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function chatMessage()
  {
    return $this->belongsTo(ChatMessage::class, 'chat_message_id', 'id');
  }
}
