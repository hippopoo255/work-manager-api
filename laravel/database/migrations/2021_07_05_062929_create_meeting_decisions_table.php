<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingDecisionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('meeting_decisions', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('meeting_record_id')->comment('議事録ID');
      $table->unsignedBigInteger('decided_by')->nullable()->comment('決定者');
      $table->unsignedBigInteger('created_by')->comment('入力者');
      $table->string('subject')->nullable()->comment('議題');
      $table->string('body')->comment('決定内容');

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('meeting_record_id')->references('id')->on('meeting_records')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('decided_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('created_by')->references('id')->on('users')
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
    Schema::table('meeting_decisions', function (Blueprint $table) {
      $table->dropForeign('meeting_decisions_decided_by_foreign');
      $table->dropForeign('meeting_decisions_created_by_foreign');
      $table->dropForeign('meeting_decisions_meeting_record_id_foreign');
    });
    Schema::dropIfExists('meeting_decisions');
  }
}
