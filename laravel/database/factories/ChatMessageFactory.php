<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;

use Faker\Generator as Faker;
use Faker\Factory;

$faker = Factory::create('ja_JP');
$factory->define(ChatMessage::class, function ($faker) {
  return [
    'chat_room_id' => array_random(ChatRoom::all()->pluck('id')->toArray()),
    'created_by' => array_random(User::all()->pluck('id')->toArray()),
    'mentioned_to' => null,
    'body' => $faker->realText,
  ];
});
