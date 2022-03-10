<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress', function (Blueprint $table) {
      $table->id();
      $table->string('name')->comment('「未着手」など');
      $table->integer('value')->default(1)->comment('達成値（数値が大きいほど完成度が高い）');
      $table->unsignedBigInteger('created_by')->comment('作成者');
      $table->unsignedBigInteger('updated_by')->nullable()->comment('更新者');
      $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除者');

      $table->timestamps();

      $table->foreign('created_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('updated_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('deleted_by')->references('id')->on('users')
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
    Schema::table('progress', function (Blueprint $table) {
      $table->dropForeign('progress_deleted_by_foreign');
      $table->dropForeign('progress_updated_by_foreign');
      $table->dropForeign('progress_created_by_foreign');
    });
    Schema::dropIfExists('progress');
  }
}
