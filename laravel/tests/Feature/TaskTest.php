<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskTest extends TestCase
{
  /**
   * @test
   * @group task
   */
  public function should_認証者のタスク一覧()
  {
    $response = $this->actingAs($this->user)->getJson(route('task.findByOwner'));
    $response->assertOk()->assertJsonStructure([
      'data',
      'first_page_url',
      'from',
      'last_page',
      'last_page_url',
      'next_page_url',
      'path',
      'per_page',
      'prev_page_url',
      'to',
      'total',
    ]);

    $result = parent::$openApiValidator->validate('getTask', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group task
   */
  public function should_タスクの投稿()
  {
    $expects = [
      'owner_id' => $this->user->id,
      'body' => Str::random(32),
      'time_limit' => Carbon::today()->format('Y/m/d H:i'),
    ];
    $response = $this->actingAs($this->user)->postJson(route('task.store'), $expects);
    $response->assertCreated()->assertJson([
      'body' => $expects['body'],
    ]);
    $this->assertDatabaseHas('tasks', [
      'id' => $response->getOriginalContent()->id,
      'body' => $expects['body'],
      'created_by' => $this->user->id,
    ]);

    $result = parent::$openApiValidator->validate('postTask', 201, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group task
   */
  public function should_タスク投稿のバリデーションエラー()
  {
    $invalid = [
      'created_by' => 111111, // 存在しないユーザID
      'body' => Str::random(81), // 80文字を超えている
      'time_limit' => Carbon::today()->format('Y-m-d H:i:s'), // 日付の形式が違う
      'priority_id' => 111111, // 存在しない優先度ID
      'progress_id' => 111111, // 存在しない進捗度ID
    ];
    $response = $this->actingAs($this->user)->postJson(route('task.store'), $invalid);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'created_by', 'owner_id', 'priority_id', 'progress_id', 'body', 'time_limit',
    ]);
    $this->assertDatabaseMissing('tasks', [
      'created_by' => $invalid['created_by'],
      'body' => $invalid['body'],
    ]);

    $result = parent::$openApiValidator->validate('postTask', 422, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group task
   */
  public function should_タスクの更新()
  {
    $task = Task::factory()->create([
      'owner_id' => $this->user->id,
      'created_by' => $this->user->id,
    ]);
    $expects = [
      'owner_id' => $this->user->id,
      'body' => $task->body . '_update',
      'time_limit' => Carbon::today()->format('Y/m/d H:i'),
    ];
    $response = $this->actingAs($this->user)->putJson(route('task.update', $task), $expects);
    $response->assertOk()->assertJson([
      'id' => $task->id,
      'body' => $expects['body'],
    ]);
    $this->assertDatabaseHas('tasks', [
      'id' => $response->getOriginalContent()->id,
      'body' => $expects['body'],
    ]);

    $result = parent::$openApiValidator->validate('putTaskId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group task
   */
  public function should_タスクの追加者以外による更新禁止()
  {
    $task = Task::factory()->create([
      'owner_id' => $this->user->id,
      'created_by' => $this->user->id,
    ]);
    $badUser = User::where('id', '!=', $this->user->id)->first();
    $willDenied = [
      'owner_id' => $badUser->id,
      'body' => $task->body . '_update',
      'time_limit' => Carbon::today()->format('Y/m/d H:i'),
    ];
    $response = $this->actingAs($badUser)->putJson(route('task.update', $task), $willDenied);
    $response->assertForbidden();
    $this->assertDatabaseMissing('tasks', [
      'id' => $task->id,
      'body' => $willDenied['body'],
    ]);

    $result = parent::$openApiValidator->validate('putTaskId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group task
   */
  public function should_タスクの削除()
  {
    $task = Task::factory()->create([
      'owner_id' => $this->user->id,
      'created_by' => $this->user->id,
    ]);
    $response = $this->actingAs($this->user)->deleteJson(route('task.destroy', $task));
    $response->assertNoContent();
    $this->assertSoftDeleted('tasks', [
      'id' => $task->id,
    ]);

    $result = parent::$openApiValidator->validate('deleteTaskId', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group task
   */
  public function should_投稿者以外のユーザによるタスクの削除禁止()
  {
    $task = Task::factory()->create([
      'owner_id' => $this->user->id,
      'created_by' => $this->user->id,
    ]);
    $badUser = User::where('id', '!=', $this->user->id)->first();
    $response = $this->actingAs($badUser)->deleteJson(route('task.destroy', $task));

    $response->assertForbidden();
    $this->assertDatabaseHas('tasks', [
      'id' => $task->id,
      'deleted_at' => null,
    ]);

    $result = parent::$openApiValidator->validate('deleteTaskId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
