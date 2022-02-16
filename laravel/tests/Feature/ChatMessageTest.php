<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatMessageTest extends ChatRoomTest
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->chatRoom->members()->sync($this->membersData);
  }

  /**
   * @test
   * @group chat_message
   */
  public function should_メッセージの投稿()
  {
    $expects = [
      'body' => 'This is a message.',
      'created_by' => $this->user->id,
    ];
    $response = $this->actingAs($this->user)->postJson(route('chatMessage.store', $this->chatRoom), $expects);
    $new = $this->chatRoom->messages->first();
    $response->assertCreated()->assertJson([
      'id' => $new->id,
      'body' => $expects['body'],
    ]);
    $this->assertDatabaseHas('chat_messages', [
      'id' => $new->id,
      'body' => $expects['body'],
    ]);

    $result = parent::$openApiValidator->validate('postChatMessage', 201, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_message
   */
  public function should_グループメンバ以外のユーザによる投稿は禁止()
  {
    $badUser = User::whereNotIn('id', array_keys($this->membersData))->get()->first();
    $willDenied = [
      'body' => 'This is a message.',
      'created_by' => $badUser->id,
    ];
    $response = $this->actingAs($badUser)->postJson(route('chatMessage.store', $this->chatRoom), $willDenied);
    $response->assertForbidden();

    $result = parent::$openApiValidator->validate('postChatMessage', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_message
   */
  public function should_投稿者以外による更新は禁止()
  {
    $chatMessage = factory(ChatMessage::class)->create([
      'body' => 'This is a message.',
      'created_by' => $this->user->id,
      'chat_room_id' => $this->chatRoom->id,
    ]);

    $badUser = User::whereNotIn('id', [$chatMessage->created_by])->get()->first();
    $willDenied = [
      'created_by' => $this->user->id,
      'body' => 'update_' . $chatMessage->body,
    ];

    $response = $this->actingAs($badUser)->putJson(route('chatMessage.update', [
      'id' => $this->chatRoom,
      'chat_message_id' => $chatMessage,
    ]), $willDenied);

    $response->assertForbidden();

    $result = parent::$openApiValidator->validate('putChatMessageId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_message
   */
  public function should_メッセージの更新()
  {
    $chatMessage = factory(ChatMessage::class)->create([
      'body' => 'This is a message.',
      'created_by' => $this->user->id,
      'chat_room_id' => $this->chatRoom->id,
    ]);
    $count = ChatMessage::count();
    $expects = [
      'created_by' => $this->user->id,
      'body' => 'update_' . $chatMessage->body,
    ];
    $response = $this->actingAs($this->user)->putJson(route('chatMessage.update', [
      'id' => $this->chatRoom,
      'chat_message_id' => $chatMessage,
    ]), $expects);
    $response->assertOk()->assertJson([
      'id' => $chatMessage->id,
      'body' => $expects['body'],
    ]);
    $this->assertDatabaseHas('chat_messages', [
      'id' => $chatMessage->id,
      'body' => $expects['body'],
    ])->assertDatabaseCount('chat_messages', $count);

    $result = parent::$openApiValidator->validate('putChatMessageId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_message
   */
  public function should_投稿者以外はメッセージの削除禁止()
  {
    $chatMessage = factory(ChatMessage::class)->create([
      'body' => 'This is a message.',
      'created_by' => $this->user->id,
      'chat_room_id' => $this->chatRoom->id,
    ]);

    $badUser = User::whereNotIn('id', [$chatMessage->created_by])->get()->first();

    $response = $this->actingAs($badUser)->deleteJson(route('chatMessage.destroy', [
      'id' => $this->chatRoom,
      'chat_message_id' => $chatMessage,
    ]));

    $response->assertForbidden();

    $result = parent::$openApiValidator->validate('deleteChatMessageId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group chat_message
   */
  public function should_メッセージの削除()
  {
    $chatMessage = factory(ChatMessage::class)->create([
      'body' => 'This is a message.',
      'created_by' => $this->user->id,
      'chat_room_id' => $this->chatRoom->id,
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('chatMessage.destroy', [
      'id' => $this->chatRoom,
      'chat_message_id' => $chatMessage,
    ]));
    $response->assertNoContent();
    $this->assertSoftDeleted('chat_messages', [
      'id' => $chatMessage->id,
    ]);

    $result = parent::$openApiValidator->validate('deleteChatMessageId', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
