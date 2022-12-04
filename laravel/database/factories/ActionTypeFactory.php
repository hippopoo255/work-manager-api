<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActionTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'key' => $this->faker->unique()->word,
        'label_name' => $this->faker->word,
        'template_message' => $this->faker->sentence(2),
        'link' => '/',
      ];
    }
}
