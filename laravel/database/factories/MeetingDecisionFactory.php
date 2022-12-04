<?php

namespace Database\Factories;

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
class MeetingDecisionFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'meeting_record_id' => $this->faker->randomNumber,
      'decided_by' => $this->faker->numberBetween(1, 10),
      'created_by' => $this->faker->numberBetween(1, 10),
      'subject' => '決定内容件名',
      'body' => '決定内容xxxxxxxxxx',
    ];
  }
}
