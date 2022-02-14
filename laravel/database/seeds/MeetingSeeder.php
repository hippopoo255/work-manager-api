<?php

use Illuminate\Database\Seeder;
use App\Models\MeetingRecord;
use App\Models\MeetingDecision;
use App\Models\User;

class MeetingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('tasks')->truncate();
    DB::table('meeting_decisions')->truncate();
    DB::table('meeting_members')->truncate();
    DB::table('meeting_records')->truncate();
    for ($i = 0; $i < 100; $i++) {
      $members = User::all()->pluck('id')->shuffle()->splice(0, random_int(2, 15))->all();
      $createdBy = array_random($members);
      // 議事録の追加
      $meetingRecord = factory(MeetingRecord::class)->create([
        'created_by' => $createdBy,
      ]);
      // 参加者の追加
      $meetingRecord->members()->sync($members);
      // 決議事項の追加
      $meetingDecisions = factory(MeetingDecision::class, random_int(1, 4))->create([
        'meeting_record_id' => $meetingRecord->id,
        'decided_by' => array_random($members),
        'created_by' => $createdBy,
      ]);
      // 議事録からのTaskの追加
      // $meetingDecisions->each(function ($meetingDecision) {
      //   factory(Task::class, 1)->create([
      //     'meeting_decision_id' => $meetingDecision->id,
      //     'owner_id' => array_random(User::all()->pluck('id')->toArray()),
      //     'created_by' => $meetingDecision->created_by,
      //   ]);
      // });
    }
  }
}
