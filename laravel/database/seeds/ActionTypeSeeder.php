<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ActionType;
use App\Models\NotifyValidation;

class ActionTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('activities')->truncate();
    DB::table('notify_validations')->truncate();
    DB::table('action_types')->truncate();

    $types = [
      [
        'key' => 'meeting_record_joined',
        'label_name' => '議事録が追加されたとき',
        'template_message' => ':fromさんから議事録「:body」が追加されました',
        'link' => '/mypage/meeting_record/:id',
        'is_notify' => 1,
      ],
      [
        'key' => 'schedule_shared',
        'label_name' => '新たなスケジュールが共有されたとき',
        'template_message' => ':fromさんからスケジュール「:body」が共有されました',
        'link' => '/mypage/schedule',
        'is_notify' => 1,
      ],
      [
        'key' => 'message_sent',
        'label_name' => '所属するチャットグループにメッセージが届いた時',
        'template_message' => ':fromさんが新着メッセージ「:body」を投稿しました',
        'link' => '/mypage/chat/:id',
        'is_notify' => 1,
      ],
      [
        'key' => 'daily_limit_task',
        'label_name' => '翌日締切のタスク（前日夕方）',
        'template_message' => '',
        'link' => '/mypage/task',
        'is_notify' => 1,
      ],
      [
        'key' => 'daily_schedule',
        'label_name' => '当日の予定（当日朝）',
        'template_message' => '',
        'link' => '/mypage/schedule',
        'is_notify' => 1,
      ],
      [
        'key' => 'blog_report',
        'label_name' => 'ブログの報告',
        'template_message' => '',
        'link' => '/blog/report',
      ],
      [
        'key' => 'chat_report',
        'label_name' => 'チャットの報告',
        'template_message' => ':fromさんがチャット「:body」を報告しました',
        'link' => '/chat/report',
      ],
      [
        'key' => 'user_create',
        'label_name' => '一般アカウントの追加',
        'template_message' => ':fromさんが一般アカウント「:bodyさん」を追加しました',
        'is_admin' => 1,
        'link' => '/user',
      ],
      [
        'key' => 'admin_create',
        'label_name' => '管理者アカウントの追加',
        'template_message' => ':fromさんが管理者アカウントに「:bodyさん」を招待しました',
        'is_admin' => 1,
        'link' => '/admin',
      ],
      [
        'key' => 'meeting_place_create',
        'label_name' => '会議室の追加',
        'template_message' => ':fromさんが会議室に「:body」を追加しました',
        'is_admin' => 1,
        'link' => '/master/meeting_room',
      ],
      [
        'key' => 'department_create',
        'label_name' => '部署の追加',
        'template_message' => ':fromさんが部署に「:body」を追加しました',
        'is_admin' => 1,
        'link' => '/master/department',
      ],
      [
        'key' => 'progress_create',
        'label_name' => '進捗度の追加',
        'template_message' => ':fromさんが進捗度に「:body」を追加しました',
        'is_admin' => 1,
        'link' => '/master/progress',
      ],
      [
        'key' => 'organization_update',
        'label_name' => '組織情報の変更',
        'template_message' => ':fromさんが組織情報を変更しました',
        'is_admin' => 1,
        'link' => '/organization',
      ],
    ];

    $actionTypeIds = collect($types)->map(function ($type) {
      $newActionType = ActionType::factory()->create($type);
      return $newActionType;
    })->filter(function ($type) {
      return $type->is_notify;
    })->pluck('id');

    $users = User::all();

    $users->each(function ($user) use ($actionTypeIds) {
      foreach ($actionTypeIds as $actionTypeId) {
        $user->notifyValidations()->create([
          'action_type_id' => (int)$actionTypeId,
          'is_valid' => 0,
        ]);
      }
    });
  }
}
