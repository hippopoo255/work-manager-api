<?php

namespace App\Services;

use App\Contracts\Repositories\ChatRoomRepositoryInterface as Repository;
use App\Models\ChatRoom;
use App\Contracts\Queries\ChatRoomQueryInterface as Query;
use App\Contracts\Repositories\UserRepositoryInterface as UserRepository;
use App\Services\Supports\WithRepositoryTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatRoomService extends Service
{
  use WithRepositoryTrait;

  /**
   * @var UserRepository
   */
  private $userRepository;

  /**
   * UserService constructor.
   * @param Repository $repository
   * @param Query $query
   */
  public function __construct(
    Repository $repository,
    Query $query,
    UserRepository $userRepository
  ) {
    $this->setRepository($repository);
    $this->setQuery($query);
    $this->userRepository = $userRepository;
  }

  /**
   * @return array
   */
  public function findByOwner()
  {
    $author = Auth::user();
    if (!!$author) {
      $loads = [
        'members',
        'messages.createdBy',
        'messages.to',
        'messages.createdBy',
        'messages.images',
      ];
      $chatRooms = $author->chatRooms;
      $chatRooms->load($loads);
      return $chatRooms;
    }
    return [];
  }

  /**
   * @param array $params
   * @return ChatRoom
   */
  public function store(array $params): ChatRoom
  {
    $params = $this->addMe($params);
    // ルーム名が指定されていない時は代わりに参加者の名前を連ねる
    if (empty($params['name'] ?? '')) {
      $params['name'] = Str::limit($this->userRepository->names(array_keys($params['members'])), 191);
    }
    return $this->repository()->saveWithMembers($params);
  }

  /**
   * @param $id
   * @param array $loads
   * @return ChatRoom
   */
  public function find($id, ?array $loads = null): ChatRoom
  {
    return $this->repository()->find($id, $loads ?? $this->query()->relation());
  }

  /**
   * @param array $params
   * @param $id
   * @return ChatRoom
   */
  public function update(array $params, $id): ChatRoom
  {
    return $this->repository()->saveWithMembers($params, 'members', $id);
  }

  /**
   * @param $id
   * @return ChatRoom
   */
  public function delete($id): ChatRoom
  {
    return $this->repository()->delete($id);
  }

  /**
   * @param array $params
   * @return array
   */
  private function addMe(array $params)
  {
    $params['members'][Auth::user()->id] = [
      'is_editable' => true,
      'shared_by' => Auth::user()->id,
    ];
    return $params;
  }
}
