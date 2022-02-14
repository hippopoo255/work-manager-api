<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingRecordsTable extends Migration
{
  /**
   * @return void
   */
  public function up()
  {
    Schema::create('meeting_records', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('created_by')->comment('議事録作成者');
      $table->unsignedBigInteger('place_id')->nullable()->comment('開催場所');
      $table->unsignedBigInteger('role_id')->default(1)->comment('権限ID');
      $table->dateTime('meeting_date')->comment('開催日');
      $table->string('title')->comment('会議名');
      $table->text('summary')->nullable()->comment('ミーティング概要');
      $table->unsignedBigInteger('updated_by')->nullable()->comment('議事録更新者');
      $table->unsignedBigInteger('deleted_by')->nullable()->comment('議事録削除者');

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('created_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('updated_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('deleted_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('place_id')->references('id')->on('meeting_places')->onDelete('no action');
    });
  }

  /**
   * @return void
   */
  public function down()
  {
    Schema::table('meeting_records', function (Blueprint $table) {
      $table->dropForeign('meeting_records_place_id_foreign');
      $table->dropForeign('meeting_records_deleted_by_foreign');
      $table->dropForeign('meeting_records_updated_by_foreign');
      $table->dropForeign('meeting_records_created_by_foreign');
    });
    Schema::dropIfExists('meeting_records');
  }
}
