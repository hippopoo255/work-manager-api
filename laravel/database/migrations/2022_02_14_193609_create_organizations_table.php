<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('organizations', function (Blueprint $table) {
      $table->id();
      $table->string('name')->nullable()->comment('会社名');
      $table->string('name_kana')->nullable()->comment('会社名カナ');
      $table->unsignedBigInteger('pref_id')->nullable()->comment('都道府県ID');
      $table->string('city')->nullable()->comment('市区町村');
      $table->string('address')->nullable()->comment('所在場所');
      $table->string('tel')->nullable()->comment('電話番号');
      $table->unsignedBigInteger('supervisor_id')->nullable()->comment('責任者');

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('pref_id')->references('id')->on('prefectures')
        ->onUpdate('cascade')
        ->onDelete('no action');
      $table->foreign('supervisor_id')->references('id')->on('users')
        ->onUpdate('cascade')
        ->onDelete('no action');
    });

    Schema::table('organizations', function () {
      $sql = 'ALTER TABLE organizations ADD postal_code INT(7) ZEROFILL AFTER name_kana';
      DB::statement($sql);
    });

    Schema::table('users', function (Blueprint $table) {
      $table->unsignedBigInteger('organization_id')->nullable()->after('login_id')->comment('会社ID');
      $table->foreign('organization_id')->references('id')->on('organizations')
        ->onUpdate('cascade')
        ->onDelete('no action');
    });

    Schema::table('admins', function (Blueprint $table) {
      $table->unsignedBigInteger('organization_id')->nullable()->after('login_id')->comment('会社ID');
      $table->foreign('organization_id')->references('id')->on('organizations')
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
    Schema::table('admins', function (Blueprint $table) {
      $table->dropForeign('admins_organization_id_foreign');
      $table->dropColumn('organization_id');
    });
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign('users_organization_id_foreign');
      $table->dropColumn('organization_id');
    });
    Schema::table('organizations', function (Blueprint $table) {
      $table->dropForeign('organizations_supervisor_id_foreign');
      $table->dropForeign('organizations_pref_id_foreign');
    });
    Schema::dropIfExists('organizations');
  }
}
