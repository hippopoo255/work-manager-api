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
class RoleFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'label' => $this->faker->unique()->numberBetween(1, 100),
      'name' => $this->faker->unique()->jobTitle,
      'value' => $this->faker->unique()->numberBetween(1, 20),
    ];
  }
}
