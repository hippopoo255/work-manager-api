<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mmal\OpenapiValidator\Validator;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
  protected $admin;
  protected $naked = 'password';

  protected function setUp(): void
  {
    parent::setUp();
    $this->admin = Admin::first();
  }

  /**
   * @test
   * @group auth
   */
  public function should_認証中なら認証情報を返却する()
  {
    $response = $this->actingAs($this->admin, 'admin')->getJson(route('admin.currentAdmin'));
    $response->assertOk()->assertJson([
      'id' => $this->admin->id
    ]);
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('getCurrentAdmin', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group auth
   */
  public function should_未認証なら空のJSONを返却する()
  {
    $response = $this->getJson(route('admin.currentAdmin'));
    $response->assertOk()->assertExactJson([]);
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('getCurrentAdmin', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group auth
   */
  public function should_ログインに成功すると認証中のユーザーを返却する()
  {
    $expectData = [
      'last_name' => 'expectadmin',
      'first_name' => 'expectadmin',
      'email' => 'expect@example.com',
      'login_id' => 'expectadmin',
      'password' => \Hash::make($this->naked),
    ];
    $expectAdmin = Admin::create($expectData);

    $response = $this->postJson(route('admin.login'), [
      'login_id' => $expectAdmin->login_id,
      'password' => $this->naked,
    ]);

    $response->assertOk()->assertJson([
      'id' => $expectAdmin->id,
      'email' => $expectAdmin->email,
    ]);

    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('postAdminLogin', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group auth
   */
  public function should_送信データが不正だと422を返却する()
  {
    // 管理ユーザー保存
    $expectData = [
      'last_name' => 'expectadmin2',
      'first_name' => 'expectadmin2',
      'email' => 'expect2@example.com',
      'login_id' => 'expectadmin2',
      'password' => \Hash::make($this->naked),
    ];
    $expectAdmin = Admin::create($expectData);

    // IDが間違っている場合
    $invalidId = [
      'login_id' =>  'invalidadmin',
      'password' =>  $this->naked,
    ];
    $response = $this->postJson(route('admin.login'), $invalidId);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'login_id'
    ]);

    // パスワードが間違っている場合
    $invalidPassword = [
      'login_id' =>  $expectData['login_id'],
      'password' =>  'invalsidpassword',
    ];
    $response = $this->postJson(route('admin.login'), $invalidPassword);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'login_id'
    ]);

    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('postAdminLogin', 422, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group auth
   */
  public function should_認証中にログインAPIにアクセスするとリダイレクトコードを返却する()
  {
    $expectData = [
      'last_name' => 'expectadmin',
      'first_name' => 'expectadmin',
      'email' => 'author@example.com',
      'login_id' => 'expectauthor',
      'password' => \Hash::make($this->naked),
    ];
    $expectAdmin = Admin::create($expectData);

    $response = $this->actingAs($expectAdmin, 'admin')->postJson(route('admin.login'), [
      'login_id' => $expectAdmin->login_id,
      'password' => $this->naked,
    ]);
    $response->assertStatus(302);
  }
  /**
   * @test
   * @group auth
   */
  public function should_認証中にログアウトをすると空のJsonを返却する()
  {
    $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.logout'));
    $response->assertOk()->assertExactJson([]);

    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('postAdminLogout', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group auth
   */
  public function should_未認証でログアウトAPIにアクセスすると401を返却する()
  {
    $response = $this->postJson(route('admin.logout'));
    $response->assertUnauthorized();
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('postAdminLogout', 401, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
