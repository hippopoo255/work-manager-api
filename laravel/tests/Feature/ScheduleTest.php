<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\ScheduleShare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\ActionType;
use App\Contracts\Repositories\ActionTypeRepositoryInterface as ActionTypeRepository;

class ScheduleTest extends TestCase
{
  protected $members;
  protected $schedule;
  protected $membersData;

  protected function setUp(): void
  {
    parent::setUp();
    $this->schedule = factory(Schedule::class)->create([
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
   * @group schedule
   */
  public function should_スケジュールの作成()
  {
    $count = ScheduleShare::count();
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
      'created_by' => $this->user->id,
      'title' => 'This is a schedule',
      'start' => Carbon::today()->format('Y/m/d H:i'),
      'end' => Carbon::today()->addHour()->format('Y/m/d H:i'),
      'color' => '#ac1539',
      'sharedMembers' => $members,
    ];
    $response = $this->actingAs($this->user)->postJson(route('schedule.store'), $expects);
    $response->assertCreated();
    $new = json_decode($response->getContent(), true);
    // リクエスト送信後、メンバー保存前に作成者を加えているため+1している
    $newCount = $count + count($expects['sharedMembers']) + 1;
    $this->assertDatabaseHas('schedules', [
      'id' => $new['id']
    ])->assertDatabaseCount('schedule_shares', $newCount);

    $result = parent::$openApiValidator->validate('postSchedule', 201, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group schedule
   */
  public function should_編集権限のないユーザによる更新禁止()
  {
    $this->schedule->sharedMembers()->sync($this->membersData);
    $willDenied = [
      'created_by' => $this->user->id,
      'title' => $this->schedule . '_update',
      'start' => Carbon::today()->format('Y/m/d H:i'),
      'end' => Carbon::today()->addHour()->format('Y/m/d H:i'),
      'color' => '#ac1539',
    ];
    // 編集権限のない共有者
    $response = $this->actingAs($this->user)->putJson(route('schedule.update', $this->schedule), $willDenied);
    $response->assertForbidden();
    $this->assertDatabaseMissing('schedules', [
      'id' => $this->schedule->id,
      'title' => $willDenied['title'],
    ]);
    // そもそも共有されていないユーザ
    $badUser = User::whereNotIn('id', array_keys($this->membersData))->get()->first();
    $response = $this->actingAs($badUser)->putJson(route('schedule.update', $this->schedule), $willDenied);
    $response->assertForbidden();
    $this->assertDatabaseMissing('schedules', [
      'id' => $this->schedule->id,
      'title' => $willDenied['title'],
    ]);

    $result = parent::$openApiValidator->validate('putScheduleId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group schedule
   */
  public function should_スケジュールの更新()
  {
    $this->schedule->sharedMembers()->sync($this->membersData);
    $count = ScheduleShare::count();
    $updatedMembersData = $this->membersData;
    array_pop($updatedMembersData);
    $expects = [
      'created_by' => $this->schedule->created_by,
      'title' => $this->schedule->title . '_update',
      'start' => Carbon::today()->format('Y/m/d H:i'),
      'end' => Carbon::today()->addHour()->format('Y/m/d H:i'),
      'sharedMembers' => $updatedMembersData,
    ];
    $response = $this->actingAs($this->members[0])->putJson(route('schedule.update', $this->schedule), $expects);
    $response->assertOk()->assertJson([
      'title' => $expects['title']
    ]);
    $this->assertDatabaseHas('schedules', [
      'id' => $this->schedule->id,
      'title' => $expects['title'],
    ])->assertDatabaseCount('schedule_shares', $count - 1);

    $result = parent::$openApiValidator->validate('putScheduleId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group schedule
   */
  public function should_編集権限のないユーザはスケジュールを削除できない()
  {
    $this->schedule->sharedMembers()->sync($this->membersData);
    // 編集権限がない共有者
    $response = $this->actingAs($this->user)->deleteJson(route('schedule.destroy', $this->schedule));
    $response->assertForbidden();
    $this->assertDatabaseHas('schedules', [
      'id' => $this->schedule->id,
      'deleted_at' => null,
    ]);
    // そもそも共有されていないユーザ
    $badUser = User::whereNotIn('id', array_keys($this->membersData))->get()->first();
    $response = $this->actingAs($badUser)->deleteJson(route('schedule.destroy', $this->schedule));
    $response->assertForbidden();
    $this->assertDatabaseHas('schedules', [
      'id' => $this->schedule->id,
      'deleted_at' => null,
    ]);
    $result = parent::$openApiValidator->validate('deleteScheduleId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group schedule
   */
  public function should_スケジュールの削除()
  {
    $this->schedule->sharedMembers()->sync($this->membersData);
    $response = $this->actingAs($this->members[0])->deleteJson(route('schedule.destroy', $this->schedule));
    $response->assertNoContent();
    $this->assertSoftDeleted('schedules', [
      'id' => $this->schedule->id,
    ]);

    $result = parent::$openApiValidator->validate('deleteScheduleId', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
