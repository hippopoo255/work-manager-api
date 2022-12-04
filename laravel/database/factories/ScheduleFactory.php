<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
class ScheduleFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $today = Carbon::today();
    $start = $today->addDays(random_int(1, 10));

    return [
      'created_by' => $this->faker->sentence(2),
      'title' => $this->faker->sentence(2),
      'start' => $start,
      'end' => $start->addHours(random_int(1, 2)),
    ];
  }
}
