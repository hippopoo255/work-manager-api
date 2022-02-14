<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('chat_messages', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('chat_room_id')->comment('ルームID');
      $table->unsignedBigInteger('created_by')->comment('投稿者');
      $table->unsignedBigInteger('mentioned_to')->nullable()->comment('メンション相手');
      $table->text('body')->comment('メッセージ');

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('chat_room_id')->references('id')->on('chat_rooms')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('created_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('mentioned_to')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('chat_messages', function (Blueprint $table) {
      $table->dropForeign('chat_messages_created_by_foreign');
      $table->dropForeign('chat_messages_mentioned_to_foreign');
      $table->dropForeign('chat_messages_chat_room_id_foreign');
    });
    Schema::dropIfExists('chat_messages');
  }
}
