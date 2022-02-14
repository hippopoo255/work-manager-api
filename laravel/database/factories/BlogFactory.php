<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Blog;
use Faker\Generator as Faker;
use Faker\Factory;

$faker = Factory::create('ja_JP');
$factory->define(Blog::class, function ($faker) {
  return [
    'created_by' => $faker->numberBetween(1, 10),
    'title' => $faker->lastName,
    'body' => $faker->realText,
  ];
});
