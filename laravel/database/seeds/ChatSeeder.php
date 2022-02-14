<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;

class ChatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('last_reads')->truncate();
    DB::table('chat_message_reads')->truncate();
    DB::table('chat_message_images')->truncate();
    DB::table('chat_messages')->truncate();
    DB::table('chat_rooms')->truncate();
    DB::table('chat_room_shares')->truncate();
    $managerRole = '3';
    $managers = User::with(['department'])->where('role_id', $managerRole)->get();
    $managers->each(function ($manager) {
      // 部署のチャットルーム作成
      $room = factory(ChatRoom::class)->create([
        'created_by' => $manager->id,
        'name' => $manager->department->name . 'のルーム',
      ]);
      // 部署のチャットルームにメンバー追加
      $manager->department->members->each(function ($member) use ($manager, $room) {
        $room->members()->attach([$member->id => [
          'shared_by' => $manager->id,
          'is_editable' => 1,
        ]]);
      });
      factory(ChatMessage::class)->create([
        'chat_room_id' => $room->id,
        'created_by' => $manager->id,
      ]);
    });
  }
}
