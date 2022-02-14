<?php

namespace App\Repositories;

use App\Contracts\Repositories\LastReadRepositoryInterface;
use App\Models\LastRead;
use App\Repositories\Abstracts\EloquentRepository;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;

class LastReadRepository extends EloquentRepository implements LastReadRepositoryInterface
{
  public function __construct(LastRead $model)
  {
    $this->setModel($model);
  }

  /**
   * Undocumented function
   *
   * @param ChatRoom $chatRoom
   * @param integer $lastMessageId
   * @return ChatRoom
   */
  public function saveByRoom(ChatRoom $chatRoom, int $lastMessageId): ChatRoom
  {
    // $chatRoom = $this->find($chatRoomId);
    if ($chatRoom) {
      $chatRoom->lastReads()->updateOrCreate([
        'member_id' => Auth::user()->id,
      ], [
        'chat_room_id' => $chatRoom->id,
        'last_message_id' => $lastMessageId,
      ]);
      $chatRoom->load(['messages.chatMessageReads', 'messages.createdBy']);
    }
    return $chatRoom;
  }
}
