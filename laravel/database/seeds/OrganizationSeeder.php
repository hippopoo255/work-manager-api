<?php

use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\User;

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
    $organization = factory(Organization::class, 1)->create([
      'supervisor_id' => \TestUser::id()
    ]);

    User::where('organization_id', null)->update([
      'organization_id' => $organization[0]->id,
    ]);
  }
}
