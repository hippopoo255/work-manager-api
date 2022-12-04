<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MeetingPlace;

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
class MeetingRecordFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'created_by' => $this->faker->randomNumber,
      'place_id' => array_random(MeetingPlace::all()->pluck('id')->toArray()),
      'meeting_date' => $this->faker->dateTimeThisYear,
      'title' => $this->faker->word . '会議',
      'summary' => "- 議題1\n- 議題2\n- 議題3",
    ];
  }
}
