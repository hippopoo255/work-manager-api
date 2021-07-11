<?php

namespace App\Services;

use App\Contracts\Repositories\ChatRoomRepositoryInterface as Repository;
use App\Services\Traits\WithRepositoryTrait;
use Illuminate\Support\Facades\Auth;

class ChatRoomService extends Service
{
  use WithRepositoryTrait;

  /**
   * UserService constructor.
   * @param Repository $repository
   */
  public function __construct(Repository $repository)
  {
    $this->setRepository($repository);
    // else repository...
  }

  /**
   * @return \App\Models\ChatRoom[]|array|\Illuminate\Database\Eloquent\Collection
   */
  public function findByOwner()
  {
    $author = Auth::user();
    if (!!$author) {
      $chatRooms = $author->chatRooms;
      $chatRooms->load([
        'members',
        'messages.writtenBy',
        'messages.to',
        'messages.writtenBy',
        'messages.images',
      ]);
      return $chatRooms;
    }
    return [];
  }
}
