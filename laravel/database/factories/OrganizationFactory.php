<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
class OrganizationFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->company(),
      'name_kana' => 'カブシキガイシャ',
      'postal_code' => $this->faker->postcode,
      'pref_id' => $this->faker->numberBetween(1, 47),
      'city' => $this->faker->city,
      'address' => $this->faker->streetAddress,
      // 'tel' => str_replace('-', '', $this->faker->phoneNumber),
      'tel' => '1234567890',
      'supervisor_id' => array_random(User::all()->pluck('id')->toArray()),
    ];
  }
}
