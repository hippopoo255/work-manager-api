<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
class UserFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_code' => $this->faker->unique()->numberBetween(120000, 129999),
      'role_id' => $this->faker->numberBetween(1, 3),
      'login_id' => Str::random(8),
      'cognito_sub' => Str::random(8) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(12),
      'family_name' => $this->faker->lastName,
      'given_name' => $this->faker->firstName,
      'family_name_kana' => $this->faker->lastKanaName,
      'given_name_kana' => $this->faker->firstKanaName,
      'organization_id' => 1,
      'email' => $this->faker->unique()->safeEmail,
      'email_verified_at' => now(),
      'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
      'remember_token' => Str::random(10),
    ];
  }
}
