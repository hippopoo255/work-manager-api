<?php

use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;

class OrganizationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('organizations')->truncate();
    $organization = Organization::factory()->count(1)->create([
      'supervisor_id' => \TestUser::id(),
      'updated_by' => \TestUser::id()
    ]);

    User::where('organization_id', null)->update([
      'organization_id' => $organization[0]->id,
    ]);
    Admin::where('organization_id', null)->update([
      'organization_id' => $organization[0]->id,
    ]);
  }
}
