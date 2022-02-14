<?php

namespace App\Queries;

use App\Contracts\Queries\ChatMessageQueryInterface;
use App\Models\ChatMessage;
use App\Queries\Abstracts\EloquentQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class ChatMessageQuery extends EloquentQuery implements ChatMessageQueryInterface
{
  public function __construct(
    ChatMessage $model
  ) {
    $this->setBuilder($model);
    $this->setColumns([]);
    $this->setRelationTargets([]);
  }

  /**
   * @param int $chatRoomId
   * @param int $lastMessageId
   * @return array
   */
  public function unreadIds(int $chatRoomId, int $lastMessageId): array
  {
    return $this->builder()->where('chat_room_id', $chatRoomId)->where('created_by', '<>', Auth::user()->id)->where('id', '>', $lastMessageId)->pluck('id')->toArray();
  }

  /**
   * @param int $chatRoomId
   * @param int $lastMessageId
   * @return Collection
   */
  public function unreads(int $chatRoomId, int $lastMessageId, array $with = []): Collection
  {
    return $this->builder()->with($with)->where('chat_room_id', $chatRoomId)->where('created_by', '<>', Auth::user()->id)->where('id', '>', $lastMessageId)->get();
  }
}
