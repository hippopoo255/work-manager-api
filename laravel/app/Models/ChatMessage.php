<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Contracts\Models\ModelInterface;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Models\RelationalDeleteInterface;

/**
 * App\Models\ChatMessage
 *
 * @property int $id
 * @property int $chat_room_id ルームID
 * @property int $created_by 投稿者
 * @property int|null $mentioned_to メンション相手
 * @property string $body メッセージ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ChatRoom $chatRoom
 * @property-read \App\Models\User $createdBy
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
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereCreatedBy($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessageImage[] $images
 * @property-read int|null $images_count
 * @property-read \App\Models\User|null $to
 * @method static \Illuminate\Database\Query\Builder|ChatMessage onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatMessage withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $chatMessageReads
 * @property-read int|null $chat_message_reads_count
 * @property-read bool $mine
 * @property-read bool $unread
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read int|null $reactions_count
 */
class ChatMessage extends Model implements RelationalDeleteInterface
{
  use SoftDeletes;

  protected $table = 'chat_messages';

  protected $fillable = [
    'chat_room_id',
    'created_by',
    'mentioned_to',
    'body',
  ];

  protected $appends = [
    'unread',
  ];

  const RELATIONS_ARRAY = [
    'createdBy',
    'to',
    'chatMessageReads',
    'images'
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

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function chatMessageReads()
  {
    return $this->belongsToMany(User::class, 'chat_message_reads', 'chat_message_id', 'member_id')->withTimestamps();
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function reactions()
  {
    return $this->hasMany(Reaction::class, 'chat_message_id', 'id');
  }

  /**
   * @return bool
   */
  public function getMineAttribute(): bool
  {
    return !Auth::check() ? false : Auth::user()->id === $this->created_by;
  }

  public function getDeleteRelations(): array
  {
    return [
      $this->images,
    ];
  }

  /**
   * @return bool
   */
  public function getUnreadAttribute()
  {
    if (!Auth::check()) {
      return false;
    }
    return !($this->mine || $this->chatMessageReads->contains(function ($member) {
      return $member->id === Auth::user()->id;
    }));
  }
}
