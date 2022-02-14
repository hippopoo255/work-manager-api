<?php

namespace App\Repositories;

use App\Contracts\Repositories\ChatMessageRepositoryInterface;
use App\Models\ChatMessage;
use App\Repositories\Abstracts\EloquentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatMessageRepository extends EloquentRepository implements ChatMessageRepositoryInterface
{
  public function __construct(ChatMessage $model)
  {
    $this->setModel($model);
  }

  /**
   * @param array $params
   * @param Model $parent
   * @param string $method
   * @param null $id
   * @return Model
   */
  public function attach(array $params, Model $parent, string $method, $id = null): Model
  {
    $params['created_by'] = Auth::user()->id;
    $chatMessage = parent::attach($params, $parent, $method, $id);
    // $chatMessage->load(['createdBy', 'to', 'chatMessageReads', 'images']);
    return $chatMessage;
  }
}
