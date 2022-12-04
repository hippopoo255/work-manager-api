<?php

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Priority;
use App\Models\Progress;
use Illuminate\Support\Carbon;

class TaskSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // MeetingSeederでtruncateするのでここでは不要
    $testUser = DB::table('users')->first();
    for ($i = 0; $i < 100; $i++) {
      $day = array_random(range(-14, 14));
      Task::factory()->create([
        'owner_id' => $testUser->id,
        'created_by' => $testUser->id,
        'time_limit' => Carbon::now()->addDays($day),
      ]);
    }
  }
}
