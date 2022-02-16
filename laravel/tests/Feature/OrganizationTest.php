<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Models\Task;
use App\Models\MeetingPlace;
use App\Models\MeetingRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class OrganizationTest extends TestCase
{
  private $nullOrgUser;

  protected function setUp(): void
  {
    parent::setUp();
    // 組織情報の追加処理はorganization_idがnullでも通らないといけない
    $this->nullOrgUser = factory(User::class)->create();
  }

  protected function tearDown(): void
  {
    // 後のAPIテストでランダムにユーザ一覧を取得する時邪魔になるので削除
    User::destroy($this->nullOrgUser->id);
  }

  /**
   * @test
   * @group organization
   */
  public function should_組織情報の追加()
  {
    $expects = [
      'name' => 'Test Organization',
      'name_kana' => 'テストオーガニゼーション',
      'postal_code' => 1111111,
      'pref_id' => 1,
      'city' => 'テスト市',
      'address' => 'テスト町1-2-3',
      'tel' => '0120345678',
    ];

    $response = $this->actingAs($this->nullOrgUser)->postJson(route('organization.store'), $expects);

    $response->assertCreated();
    $organization = Organization::latest()->first();

    $this->assertDatabaseHas('organizations', [
      'id' => $organization->id,
      'name' => $expects['name'],
      'supervisor_id' => $this->nullOrgUser->id,
    ]);
  }

  /**
   * @test
   * @group organization
   */
  public function should_未認証で組織情報の追加リクエストをすると401を返却する()
  {
    $willDenied = [
      'name' => 'Test Organization',
      'name_kana' => 'テスト オーガニゼーション',
      'postal_code' => 1234567,
      'pref_id' => 1,
      'city' => 'テスト市',
      'address' => 'テスト町1-2-3',
      'tel' => '0120345678',
    ];

    $response = $this->postJson(route('organization.store'), $willDenied);
    $response->assertUnauthorized();
  }

  /**
   * @test
   * @group organization
   */
  public function should_組織情報追加のバリデーションエラー()
  {
    $beforeCount = Organization::count();

    $invalid = [
      // 必須属性"name"がない
      'name_kana' => 'test',  // カタカナ違反
      'postal_code' => '1234',  // 5〜7桁の整数値違反
      'pref_id' => 48,  // 1~47の範囲外
      'city' => \Str::random(129),  // 128文字を超えている
      'address' => \Str::random(256),  // 255文字を超えている
      'tel' => '123456789',  // 10~11桁の整数値違反
    ];
    $response = $this->actingAs($this->nullOrgUser)->postJson(route('organization.store'), $invalid);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'name', 'name_kana', 'postal_code', 'pref_id', 'city', 'address', 'tel'
    ]);

    $this->assertDatabaseMissing('organizations', [
      'name_kana' => $invalid['name_kana'],
      'pref_id' => $invalid['pref_id'],
      'supervisor_id' => $this->nullOrgUser->id,
    ]);

    $afterCount = Organization::count();
    $this->assertEquals($beforeCount, $afterCount);

    // $result = parent::$openApiValidator->validate('postTask', 422, json_decode($response->getContent(), true));
    // $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group organization
   */
  public function should_組織情報の更新()
  {
    $organization = factory(Organization::class)->create([
      'supervisor_id' => $this->nullOrgUser->id,
    ]);
    $expect = [
      'name' => 'Test Organization',
      'name_kana' => 'テストオーガニゼーション',
      'postal_code' => 1111111,
      'pref_id' => 1,
      'city' => 'テスト市',
      'address' => 'テスト町1-2-3',
      'tel' => '0120345678',
    ];
    $response = $this->actingAs($this->nullOrgUser)->putJson(route('organization.update', $organization->id), $expect);

    $response->assertOk();

    $this->assertDatabaseHas('organizations', [
      'id' => $organization->id,
      'supervisor_id' => $this->nullOrgUser->id,
      'name_kana' => $expect['name_kana'],
    ]);

    // $result = parent::$openApiValidator->validate('putMeetingRecordId', 403, json_decode($response->getContent(), true));
    // $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * OrganizationExistsミドルウェアのテスト
   * @test
   * @group organization
   */
  public function should_責任者以外の更新リクエストにはForbiddenを返却する()
  {
    $organization = factory(Organization::class)->create([
      'supervisor_id' => $this->nullOrgUser->id,
    ]);

    $willDenied = [
      'name' => 'Test Organization',
      'name_kana' => 'テストオーガニゼーション',
      'postal_code' => 1111111,
      'pref_id' => 1,
      'city' => 'テスト市',
      'address' => 'テスト町1-2-3',
      'tel' => '0120345678',
    ];
    $user = User::where('id', '!=', $organization->supervisor_id)->first();
    $response = $this->actingAs($user)->putJson(route('organization.update', $organization->id), $willDenied);
    $response->assertForbidden();

    $this->assertDatabaseMissing('organizations', [
      'id' => $organization->id,
      'name' => $willDenied['name'],
    ]);

    // $result = parent::$openApiValidator->validate('putMeetingRecordId', 403, json_decode($response->getContent(), true));
    // $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group organization
   */
  public function should_組織情報が未登録のユーザはサービスの利用禁止()
  {
    $beforeCount = Task::count();
    $willDenied = [
      'owner_id' => $this->nullOrgUser->id,
      'body' => 'denied Task.',
      'time_limit' => Carbon::today()->format('Y/m/d H:i'),
    ];
    $response = $this->actingAs($this->nullOrgUser)->postJson(route('task.store'), $willDenied);
    $response->assertForbidden();

    $this->assertDatabaseMissing('tasks', [
      'time_limit' => $willDenied['time_limit'],
      'body' => $willDenied['body'],
      'owner_id' => $willDenied['owner_id'],
    ]);

    $afterCount = Task::count();
    $this->assertEquals($beforeCount, $afterCount);
  }

  /**
   * @test
   * @group organization
   */
  public function should_他の組織に紐づくリソースへのアクセス禁止()
  {
    $record = [
      'created_by' => $this->user->id,
      'place_id' => array_random(MeetingPlace::pluck('id')->toArray()),
      'meeting_date' => Carbon::now()->format('Y/m/d H:i'),
      'title' => '会議名',
      'summary' => 'This is a summary of the meeting from expects.',
    ];

    $newRecord = factory(MeetingRecord::class)->create($record);

    $otherOrg = factory(Organization::class)->create([
      'supervisor_id' => 20
    ]);

    $otherUser = factory(User::class)->create([
      'organization_id' => $otherOrg->id,
    ]);

    // $newRecord->createdBy->organization_id => 1
    // $otherUser->organization_id => 2
    $response = $this->actingAs($otherUser)->getJson(route('meetingRecord.show', $newRecord->id));
    $response->assertForbidden()->assertJsonMissing([
      'id' => $newRecord->id,
    ]);

    // $newRecord->createdBy->organization_id => 1
    // $this->user->organization_id => 1
    $response = $this->actingAs($this->user)->getJson(route('meetingRecord.show', $newRecord->id));
    $response->assertOk()->assertJson([
      'id' => $newRecord->id,
    ]);
  }
}
