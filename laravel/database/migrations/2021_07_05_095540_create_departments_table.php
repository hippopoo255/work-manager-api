<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('departments', function (Blueprint $table) {
      $table->id();
      $table->string('department_code')->nullable()->comment('部署コード');
      $table->string('name')->comment('部署名');
      $table->string('color')->nullable()->comment('カラー');
      $table->unsignedBigInteger('created_by')->nullable()->comment('登録者');
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
    Schema::table('departments', function (Blueprint $table) {
      $table->dropForeign('departments_deleted_by_foreign');
      $table->dropForeign('departments_updated_by_foreign');
      $table->dropForeign('departments_created_by_foreign');
    });
    Schema::dropIfExists('departments');
  }
}
