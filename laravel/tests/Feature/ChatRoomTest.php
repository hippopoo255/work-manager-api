<?php

namespace Tests\Feature;

use App\Models\ChatRoom;
use App\Models\ChatRoomShare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ChatRoomTest extends TestCase
{
  protected $members;
  protected $chatRoom;
  protected $membersData;

  protected function setUp(): void
  {
    parent::setUp();
    $this->chatRoom = ChatRoom::factory()->create([
      'created_by' => $this->user->id,
    ]);

    $this->members = User::where('id', '!=', $this->user->id)
      ->get()
      ->shuffle()
      ->splice(0, random_int(1, 15))
      ->all();
    foreach ($this->members as $member) {
      // 編集権限があるメンバー
      $this->membersData[$member->id] = [
        'is_editable' => 1,
        'shared_by' => $this->user->id
      ];
    }
    // 編集権限のない（今回削除を実行する）メンバー
    $this->membersData[$this->user->id] = [
      'is_editable' => 0,
      'shared_by' => $this->user->id
    ];
  }

  /**
   * @test
   * @group chat_room
   */
  public function should_チャットルーム一覧()
  {
    $response = $this->actingAs($this->user)->getJson(route('chatRoom.findByOwner'));
    $response->assertOk();
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('getCurrentAdmin', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_room
   */
  public function should_チャットルームの作成()
  {
    $count = ChatRoomShare::count();
    $memberIds = User::where('id', '!=', $this->user->id)
      ->get()
      ->shuffle()
      ->splice(0, random_int(1, 15))
      ->pluck('id')
      ->toArray();
    foreach ($memberIds as $id) {
      $members[$id] = [
        'is_editable' => 1,
        'shared_by' => $this->user->id,
      ];
    }
    $expects = [
      'members' => $members,
    ];
    $response = $this->actingAs($this->user)->postJson(route('chatRoom.store'), $expects);
    $response->assertCreated();
    $new = json_decode($response->getContent(), true);
    // リクエスト送信後、メンバー保存前に作成者を加えているため+1している
    $newCount = $count + count($expects['members']) + 1;
    $this->assertDatabaseHas('chat_rooms', [
      'id' => $new['id']
    ])->assertDatabaseCount('chat_room_shares', $newCount);

    $result = parent::$openApiValidator->validate('postChatRoom', 201, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_room
   */
  public function should_チャットルームの詳細情報()
  {
    $this->chatRoom->members()->sync($this->membersData);
    $response = $this->actingAs($this->user)->getJson(route('chatRoom.show', $this->chatRoom));

    $response->assertOk()->assertjson([
      'id' => $this->chatRoom->id
    ]);

    $result = parent::$openApiValidator->validate('getChatRoomId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  public function should_グループメンバ以外のユーザによる閲覧禁止()
  {
    $this->chatRoom->members()->sync($this->membersData);
    $badUser = User::whereNotIn('id', array_keys($this->membersData))->get()->first();
    $response = $this->actingAs($badUser)->getJson(route('chatRoom.show', $this->chatRoom));
    $response->assertForbidden()->assertJsonMissing([
      'id' => $this->chatRoom->id
    ]);

    $result = parent::$openApiValidator->validate('getChatRoomId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_room
   */
  public function should_編集権限のないメンバーの更新禁止()
  {
    $this->chatRoom->members()->sync($this->membersData);
    $willDenied = [
      'name' => $this->chatRoom->name . '_update',
      'members' => $this->membersData,
    ];
    $response = $this->actingAs($this->user)->putJson(route('chatRoom.update', $this->chatRoom), $willDenied);
    $response->assertForbidden();
    $this->assertDatabaseMissing('chat_rooms', [
      'id' => $this->chatRoom->id,
      'name' => $willDenied['name'],
    ]);

    $result = parent::$openApiValidator->validate('putChatRoomId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_room
   */
  public function should_チャットルームの更新()
  {
    $this->chatRoom->members()->sync($this->membersData);
    $count = ChatRoomShare::count();
    $updatedMembersData = $this->membersData;
    array_pop($updatedMembersData);
    $expects = [
      'name' => $this->chatRoom->name . '_update',
      'members' => $updatedMembersData,
    ];
    $response = $this->actingAs($this->members[0])->putJson(route('chatRoom.update', $this->chatRoom), $expects);
    $response->assertOk()->assertJson([
      'name' => $expects['name']
    ]);
    $this->assertDatabaseHas('chat_rooms', [
      'id' => $this->chatRoom->id,
      'name' => $expects['name'],
    ])->assertDatabaseCount('chat_room_shares', $count - 1);

    $result = parent::$openApiValidator->validate('putChatRoomId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_room
   */
  public function should_編集権限のない参加者はチャットルームを削除できない()
  {
    $this->chatRoom->members()->sync($this->membersData);
    $response = $this->actingAs($this->user)->deleteJson(route('chatRoom.destroy', $this->chatRoom));
    $response->assertForbidden();
    $this->assertDatabaseHas('chat_rooms', [
      'id' => $this->chatRoom->id,
      'deleted_at' => null,
    ]);

    $result = parent::$openApiValidator->validate('deleteChatRoomId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group chat_room
   */
  public function should_チャットルームの削除()
  {
    $this->chatRoom->members()->sync($this->membersData);
    $response = $this->actingAs($this->members[0])->deleteJson(route('chatRoom.destroy', $this->chatRoom));
    $response->assertNoContent();
    $this->assertSoftDeleted('chat_rooms', [
      'id' => $this->chatRoom->id,
    ]);

    $result = parent::$openApiValidator->validate('deleteChatRoomId', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
