<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RefactoryUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 120)->change();
            $table->string('password', 200)->change();
            $table->string('mobile_phone', 30)->change();
            $table->string('api_token', 120)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->change();
            $table->string('password')->change();
            $table->string('mobile_phone')->change();
            $table->string('api_token')->change();
        });
    }
}
