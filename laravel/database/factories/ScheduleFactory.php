 <?php

  /** @var \Illuminate\Database\Eloquent\Factory $factory */

  use App\Models\Schedule;
  use Faker\Generator as Faker;
  use Faker\Factory;
  use Illuminate\Support\Carbon;

  $faker = Factory::create('ja_JP');

  $factory->define(Schedule::class, function ($faker) {
    $today = Carbon::today();
    $start = $today->addDays(random_int(1, 10));
    return [
      'created_by' => $faker->sentence(2),
      'title' => $faker->sentence(2),
      'start' => $start,
      'end' => $start->addHours(random_int(1, 2)),
    ];
  });
