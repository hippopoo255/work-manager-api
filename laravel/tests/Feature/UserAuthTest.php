<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\Yaml\Yaml;
use Mmal\OpenapiValidator\Validator;

class UserAuthTest extends TestCase
{
  protected $user;
  protected $naked = 'password';

  protected function setUp(): void
  {
    parent::setUp();
    $this->user = User::first();
  }

  /**
   * @test
   * @group auth
   */
  public function should_認証中なら認証情報を返却する()
  {
    $response = $this->actingAs($this->user)->getJson(route('currentUser'));
    $response->assertOk()->assertJson([
      'id' => $this->user->id
    ]);
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('getCurrentUser', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group auth
   */
  public function should_未認証なら空のJSONを返却する()
  {
    $response = $this->getJson(route('currentUser'));
    $response->assertOk()->assertExactJson([]);
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('getCurrentUser', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group auth
   */
  public function should_ログインに成功すると認証中のユーザーを返却する()
  {
    $expectData = [
      'user_code' => 123456,
      'role_id' => 1,
      'last_name' => 'expectuser',
      'first_name' => 'expectuser',
      'email' => 'expect@example.com',
      'login_id' => 'expectuser',
      'password' => \Hash::make($this->naked),
    ];
    $expectUser = User::create($expectData);

    $response = $this->postJson(route('login'), [
      'login_id' => $expectUser->login_id,
      'password' => $this->naked,
    ]);

    $response->assertOk()->assertJson([
      'id' => $expectUser->id,
      'email' => $expectUser->email,
    ]);

    $result = parent::$openApiValidator->validate('postLogin', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);

  }

  /**
   * @test
   * @group auth
   */
  public function should_送信データが不正だと422を返却する()
  {
    // ユーザー保存
    $expectData = [
      'user_code' => 654321,
      'role_id' => 1,
      'last_name' => 'expectuser2',
      'first_name' => 'expectuser2',
      'email' => 'expect2@example.com',
      'login_id' => 'expectuser2',
      'password' => \Hash::make($this->naked),
    ];
    $expectUser = User::create($expectData);

    // IDが間違っている場合
    $invalidId = [
      'login_id' =>  'invaliduser',
      'password' =>  $this->naked,
    ];
    $response = $this->postJson(route('login'), $invalidId);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'login_id'
    ]);

    // パスワードが間違っている場合
    $invalidPassword = [
      'login_id' =>  $expectData['login_id'],
      'password' =>  'invalsidpassword',
    ];
    $response = $this->postJson(route('login'), $invalidPassword);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'login_id'
    ]);
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('postLogin', 422, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group auth
   */
  public function should_認証中にログインAPIにアクセスするとリダイレクトコードを返却する()
  {
    $expectData = [
      'user_code' => 123457,
      'role_id' => 1,
      'last_name' => 'expectuser',
      'first_name' => 'expectuser',
      'email' => 'author@example.com',
      'login_id' => 'expectauthor',
      'password' => \Hash::make($this->naked),
    ];
    $expectUser = User::create($expectData);

    $response = $this->actingAs($expectUser)->postJson(route('login'), [
      'login_id' => $expectUser->login_id,
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
    $response = $this->actingAs($this->user)->postJson(route('logout'));
    $response->assertOk()->assertExactJson([]);
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('postLogout', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group auth
   */
  public function should_未認証でログアウトAPIにアクセスすると401を返却する()
  {
    $response = $this->postJson(route('logout'));
    $response->assertUnauthorized();
    // APIの仕様とデータ形式が一致している
    $result = parent::$openApiValidator->validate('postLogout', 401, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
