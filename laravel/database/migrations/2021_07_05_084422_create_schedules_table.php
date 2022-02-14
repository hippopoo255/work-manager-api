<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('schedules', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('created_by')->comment('予定作成者');
      $table->string('title')->comment('予定の内容');
      $table->dateTime('start')->comment('開始日時');
      $table->dateTime('end')->comment('終了日時');
      $table->boolean('is_public')->default(1)->comment('公開設定');
      $table->string('color')->nullable()->comment('カラー');
      $table->text('memo')->nullable()->comment('メモ');

      $table->timestamps();
      $table->softDeletes();

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
    Schema::table('schedules', function (Blueprint $table) {
      $table->dropForeign('schedules_created_by_foreign');
    });
    Schema::dropIfExists('schedules');
  }
}
