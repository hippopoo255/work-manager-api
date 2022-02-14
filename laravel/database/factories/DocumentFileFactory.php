<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentFile;
use Faker\Generator as Faker;

$factory->define(DocumentFile::class, function (Faker $faker) {
  return [
    'created_by' => array_random(\App\Models\User::pluck('id')->toArray()),
    'folder_id' => array_random(\App\Models\DocumentFolder::pluck('id')->toArray()),
    'file_path' => 'document/' . $faker->unique()->randomLetter . '/' . $faker->unique()->randomLetter . '.jpg',
    'original_name' => $faker->unique()->randomLetter . '.jpg',
  ];
});
