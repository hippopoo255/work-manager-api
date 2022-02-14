<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogCommentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('blog_comments', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('created_by')->comment('投稿者');
      $table->unsignedBigInteger('blog_id')->comment('ブログID');
      $table->string('body')->comment('コメント');

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('created_by')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('blog_id')->references('id')->on('blogs')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('blog_comments', function (Blueprint $table) {
      $table->dropForeign('blog_comments_blog_id_foreign');
      $table->dropForeign('blog_comments_created_by_foreign');
    });
    Schema::dropIfExists('blog_comments');
  }
}
