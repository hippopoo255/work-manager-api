<?php

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\User;

class ScheduleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('schedule_shares')->truncate();
    DB::table('schedules')->truncate();
    $users = User::all();
    $users->each(function ($user) {
      $schedules = factory(Schedule::class, 10)->create([
        'created_by' => $user->id,
      ]);
      $schedules->each(function ($schedule) {
        $schedule->sharedMembers()->sync([
          $schedule->created_by => [
            'shared_by' => $schedule->created_by,
            'is_editable' => 1,
          ],
        ]);
      });
    });
  }
}
