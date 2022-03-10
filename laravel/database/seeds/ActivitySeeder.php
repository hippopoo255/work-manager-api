<?php

use App\Models\MeetingRecord;
use App\Models\Progress;
use App\Models\MeetingPlace;
use App\Models\Department;
use Illuminate\Database\Seeder;
use App\Models\ActionType;
use App\Models\Activity;
use App\Services\Supports\StrSupportTrait;

class ActivitySeeder extends Seeder
{
  use StrSupportTrait;
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::table('activities')->truncate();

    $actionTypes = ActionType::whereIn('key', [
      ActionType::MEETING_RECORD_JOINED_KEY,
      ActionType::DEPARTMENT_CREATE_KEY,
      ActionType::PROGRESS_CREATE_KEY,
      ActionType::MEETING_PLACE_CREATE_KEY,
    ])->get();

    // 議事録追加のアクティビティ
    $meetingRecordActionType = $actionTypes->firstWhere('key', ActionType::MEETING_RECORD_JOINED_KEY);
    $meetingRecords = MeetingRecord::all();
    $meetingRecords->each(function ($record) use ($meetingRecordActionType) {

      $content = $this->replaceAttribute($meetingRecordActionType->template_message, [
        'from' => $record->createdBy->full_name,
        'body' => \Str::limit(trim($record->title), 20, '（...）'),
      ]);

      $record->members->each(function ($member) use ($record, $meetingRecordActionType, $content) {
        Activity::create([
          'user_id' => $member->id,
          'created_by' => $record->created_by,
          'action_type_id' => $meetingRecordActionType->id,
          'model_id' => $record->id,
          'content' => $content,
        ]);
      });
    });

    // 部署マスター追加のアクティビティ
    $departmentActionType = $actionTypes->firstWhere('key', ActionType::DEPARTMENT_CREATE_KEY);
    $departments = Department::all();
    $departments->each(function ($record) use ($departmentActionType) {

      $content = $this->replaceAttribute($departmentActionType->template_message, [
        'from' => $record->createdBy->full_name,
        'body' => \Str::limit(trim($record->name), 20, '（...）'),
      ]);

      Activity::create([
        'created_by' => $record->created_by,
        'action_type_id' => $departmentActionType->id,
        'model_id' => $record->id,
        'content' => $content,
      ]);
    });

    // 進捗度マスター追加のアクティビティ
    $progressActionType = $actionTypes->firstWhere('key', ActionType::PROGRESS_CREATE_KEY);
    $progress = Progress::all();
    $progress->each(function ($record) use ($progressActionType) {

      $content = $this->replaceAttribute($progressActionType->template_message, [
        'from' => $record->createdBy->full_name,
        'body' => \Str::limit(trim($record->name), 20, '（...）'),
      ]);

      Activity::create([
        'created_by' => $record->created_by,
        'action_type_id' => $progressActionType->id,
        'model_id' => $record->id,
        'content' => $content,
      ]);
    });

    // 会議室マスター追加のアクティビティ
    $meetingPlaceActionType = $actionTypes->firstWhere('key', ActionType::MEETING_PLACE_CREATE_KEY);
    $meetingPlaces = MeetingPlace::all();
    $meetingPlaces->each(function ($record) use ($meetingPlaceActionType) {

      $content = $this->replaceAttribute($meetingPlaceActionType->template_message, [
        'from' => $record->createdBy->full_name,
        'body' => \Str::limit(trim($record->name), 20, '（...）'),
      ]);

      Activity::create([
        'created_by' => $record->created_by,
        'action_type_id' => $meetingPlaceActionType->id,
        'model_id' => $record->id,
        'content' => $content,
      ]);
    });
  }
}
