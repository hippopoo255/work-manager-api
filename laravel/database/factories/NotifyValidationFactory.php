<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ActionType;

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
class NotifyValidationFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_id' => array_random(User::all()->pluck('id')->toArray()),
      'action_type_id' => array_random(ActionType::all()->pluck('id')->toArray()),
      'is_valid' => 1,
    ];
  }
}
