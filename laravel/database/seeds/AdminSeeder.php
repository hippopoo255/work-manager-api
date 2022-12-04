<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('admins')->truncate();
    if (\TestUser::hasId()) {
      Admin::factory()->count(1)->create([
        'department_id' => 5,
        'user_code' => 111111,
        'role_id' => 2,
        'login_id' => config('app.test_id'),
        'cognito_sub' => config('app.test_admin_cognito_sub'),
        'password' => \Hash::make(config('app.test_pass')),
        'family_name' => 'テスト',
        'given_name' => '太郎',
        'family_name_kana' => 'テスト',
        'given_name_kana' => 'タロウ',
        'email' => config('app.test_mail', 'sample@example.com'),
        'created_by' => 1,
      ]);
    }
  }
}
