<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;

class ChatMessageFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'chat_room_id' => array_random(ChatRoom::all()->pluck('id')->toArray()),
      'created_by' => array_random(User::all()->pluck('id')->toArray()),
      'mentioned_to' => null,
      'body' => $this->faker->realText,
    ];
  }
}
