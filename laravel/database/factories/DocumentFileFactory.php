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
class DocumentFileFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'created_by' => array_random(\App\Models\User::pluck('id')->toArray()),
      'folder_id' => array_random(\App\Models\DocumentFolder::pluck('id')->toArray()),
      'file_path' => 'document/' . $this->faker->unique()->randomLetter . '/' . $this->faker->unique()->randomLetter . '.jpg',
      'original_name' => $this->faker->unique()->randomLetter . '.jpg',
    ];
  }
}
