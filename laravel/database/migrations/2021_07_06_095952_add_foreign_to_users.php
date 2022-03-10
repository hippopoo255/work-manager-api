<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignToUsers extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->unsignedBigInteger('department_id')->nullable()->after('id')->comment('部署ID');
      $table->foreign('department_id')->references('id')->on('departments')
        ->onUpdate('cascade')->onDelete('restrict');
      $table->foreign('role_id')->references('id')->on('roles')
        ->onUpdate('cascade')->onDelete('no action');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign('users_department_id_foreign');
      $table->dropForeign('users_role_id_foreign');
    });
  }
}
