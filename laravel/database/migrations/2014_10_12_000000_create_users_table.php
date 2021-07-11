<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->unsignedInteger('user_code')->unique()->comment('ユーザーコード');
      $table->unsignedBigInteger('role_id')->comment('ロールID');
      $table->string('login_id')->unique()->comment('ログインID');
      $table->string('last_name')->comment('姓');
      $table->string('first_name')->comment('名');
      $table->string('last_name_kana')->nullable()->comment('セイ');
      $table->string('first_name_kana')->nullable()->comment('メイ');
      $table->string('file_path')->nullable()->comment('画像');
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->rememberToken();
      $table->string('created_by')->nullable()->comment('登録者');
      $table->string('updated_by')->nullable()->comment('更新者');
      $table->string('deleted_by')->nullable()->comment('削除者');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('users');
  }
}
