<?php

namespace App\Services;

use App\Contracts\Queries\ChatMessageReadQueryInterface as Query;
use App\Contracts\Repositories\ChatMessageReadRepositoryInterface as Repository;
use App\Contracts\Repositories\ChatRoomRepositoryInterface as ChatRoomRepository;
use App\Contracts\Repositories\LastReadRepositoryInterface as LastReadRepository;
use App\Contracts\Repositories\UserRepositoryInterface as UserRepository;
use App\Services\Supports\WithRepositoryTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatRoom;
use App\Queries\ChatMessageQuery;

class ChatMessageReadService extends Service
{
  use WithRepositoryTrait;

  private $chatMessageQuery,
    $chatRoomRepository,
    $lastReadRepository,
    $userRepository;
  /**
   * UserService constructor.
   * @param Repository $repository
   * @param Query $query
   */
  public function __construct(
    Repository $repository,
    Query $query,
    ChatMessageQuery $chatMessageQuery,
    ChatRoomRepository $chatRoomRepository,
    LastReadRepository $lastReadRepository,
    UserRepository $userRepository
  ) {
    $this->setRepository($repository);
    $this->setQuery($query);
    $this->chatMessageQuery = $chatMessageQuery;
    $this->chatRoomRepository = $chatRoomRepository;
    $this->lastReadRepository = $lastReadRepository;
    $this->userRepository = $userRepository;
    // else repository...
  }

  /**
   * @param $chatRoomId
   * @return ChatRoom
   */
  public function store($chatRoomId): ChatRoom
  {
    list($chatRoom, $lastMessageId) = $this->chatRoomRepository->findLastMessageId($chatRoomId);
    $chatMessageIds = $this->chatMessageQuery->unreadIds($chatRoom->id, $lastMessageId);
    if (count($chatMessageIds) > 0) {
      $this->repository()->read(Auth::user()->id, $chatMessageIds);
      $chatRoom = $this->lastReadRepository->saveByRoom($chatRoom, collect($chatMessageIds)->max());
    }
    return $chatRoom;
  }

  /**
   * @param $userId
   * @return array
   */
  public function unreadByUser($userId = null): array
  {
    $user = $this->userRepository->find($userId ?? Auth::user()->id);
    $chatRoomIds = $user->chatRooms->pluck('id')->toArray();
    $unreadMessages = [];
    foreach ($chatRoomIds as $chatRoomId) {
      list($chatRoom, $lastMessageId) = $this->chatRoomRepository->findLastMessageId($chatRoomId);
      $unreadMessages[] = $this->chatMessageQuery->unreads($chatRoom->id, $lastMessageId, ['createdBy']);
    }
    $unreadMessages = collect($unreadMessages)->flatten();
    return $unreadMessages->sortByDesc('id')->values()->slice(0, 10)->all();
  }
}
