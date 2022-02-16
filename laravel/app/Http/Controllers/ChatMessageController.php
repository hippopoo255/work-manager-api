<?php

namespace App\Http\Controllers;

use App\Events\MessageDelete;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Services\ChatMessageService;
use Illuminate\Http\Request;
use App\Http\Requests\ChatMessage\StoreRequest;
use App\Http\Requests\ChatMessage\UpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSent;

class ChatMessageController extends Controller
{
  private $service;

  public function __construct(ChatMessageService $service)
  {
    $this->service = $service;
  }

  /**
   * Store a newly created resource in storage.
   * @param ChatRoom $id
   * @param StoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(ChatRoom $id, StoreRequest $request)
  {
    DB::beginTransaction();
    try {
      $chatMessage = $this->service->store($request->all(), $id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($chatMessage, 201);
  }

  /**
   * Update the specified resource in storage.
   * @param UpdateRequest $request
   * @param ChatRoom $id
   * @param ChatMessage $chat_message_id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateRequest $request, ChatRoom $id, ChatMessage $chat_message_id)
  {
    DB::beginTransaction();
    try {
      $chatMessage = $this->service->update($request->all(), $id, $chat_message_id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    broadcast(new MessageSent($chatMessage, 'update'))->toOthers();
    return response($chatMessage, 200);
  }

  /**
   * Remove the specified resource from storage.
   * @param ChatRoom $id
   * @param ChatMessage $chat_message_id
   * @return \Illuminate\Http\Response
   */
  public function destroy(ChatRoom $id, ChatMessage $chat_message_id)
  {
    $this->authorize('delete', $chat_message_id);
    \DB::beginTransaction();
    try {
      $result = $this->service->delete($chat_message_id);
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollback();
      throw $e;
    }
    broadcast(new MessageDelete($result))->toOthers();
    return response('', 204);
  }
}
