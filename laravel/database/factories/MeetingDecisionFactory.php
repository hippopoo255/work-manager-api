<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MeetingDecision;
use Faker\Generator as Faker;
use Faker\Factory;
use Illuminate\Support\Str;

$faker = Factory::create('ja_JP');

$factory->define(MeetingDecision::class, function ($faker) {
  return [
    'meeting_record_id' => $faker->randomNumber,
    'decided_by' => $faker->numberBetween(1, 10),
    'created_by' => $faker->numberBetween(1, 10),
    'subject' => '決定内容件名',
    'body' => '決定内容xxxxxxxxxx',
  ];
});
