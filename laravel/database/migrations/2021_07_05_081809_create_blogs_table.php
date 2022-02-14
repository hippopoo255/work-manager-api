<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('blogs', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('created_by')->comment('投稿者');
      $table->string('title')->comment('タイトル');
      $table->text('body')->comment('本文');

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
    Schema::table('blogs', function (Blueprint $table) {
      $table->dropForeign('blogs_created_by_foreign');
    });
    Schema::dropIfExists('blogs');
  }
}
