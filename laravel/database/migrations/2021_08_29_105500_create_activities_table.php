<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('activities', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id')->nullable()->comment('対象者');
      $table->unsignedBigInteger('created_by')->comment('誰のアクティビティか');
      $table->unsignedBigInteger('action_type_id')->comment('アクションID');
      $table->unsignedBigInteger('model_id')->nullable()->comment('モデルID');
      $table->boolean('is_read')->default(0)->comment('既読フラグ');
      $table->text('content')->comment('通知内容');
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('created_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('action_type_id')->references('id')->on('action_types')
        ->onUpdate('cascade')
        ->onDelete('cascade');
    });
  }

  /**
   * @return void
   */
  public function down()
  {
    Schema::table('activities', function (Blueprint $table) {
      $table->dropForeign('activities_user_id_foreign');
      $table->dropForeign('activities_action_type_id_foreign');
    });
    Schema::dropIfExists('activities');
  }
}
