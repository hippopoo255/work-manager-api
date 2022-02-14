<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MeetingRecord;
use App\Models\MeetingPlace;
use Faker\Generator as Faker;
use Faker\Factory;

$faker = Factory::create('ja_JP');
$factory->define(MeetingRecord::class, function ($faker) {
  return [
    'created_by' => $faker->randomNumber,
    'place_id' => array_random(MeetingPlace::all()->pluck('id')->toArray()),
    'meeting_date' => $faker->dateTimeThisYear,
    'title' => $faker->word . '会議',
    'summary' => "- 議題1\n- 議題2\n- 議題3",
  ];
});
