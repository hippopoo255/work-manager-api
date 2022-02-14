<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    Model::unguard();
    $this->setFKCheckOff();

    $this->call(PrefectureSeeder::class);
    $this->call(DepartmentSeeder::class);
    $this->call(RoleSeeder::class);
    $this->call(PrioritySeeder::class);
    $this->call(ProgressSeeder::class);
    $this->call(TagSeeder::class);
    $this->call(MeetingPlaceSeeder::class);
    $this->call(UserSeeder::class);
    $this->call(AdminSeeder::class);
    $this->call(OrganizationSeeder::class);
    $this->call(BlogSeeder::class);
    $this->call(MeetingSeeder::class);
    $this->call(TaskSeeder::class);
    $this->call(ScheduleSeeder::class);
    $this->call(ChatSeeder::class);
    $this->call(ActionTypeSeeder::class);

    $this->setFKCheckOn();
    Model::reguard();
  }

  private function setFKCheckOff()
  {
    switch (DB::getDriverName()) {
      case 'mysql':
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        break;
      case 'sqlite':
        DB::statement('PRAGMA foreign_keys = OFF');
        break;
    }
  }

  private function setFKCheckOn()
  {
    switch (DB::getDriverName()) {
      case 'mysql':
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        break;
      case 'sqlite':
        DB::statement('PRAGMA foreign_keys = ON');
        break;
    }
  }
}
