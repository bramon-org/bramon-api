<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RefactoryStationsTable extends Migration
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

        Schema::table('stations', function (Blueprint $table) {
            $table->string('name', 50)->change();
            $table->string('city', 100)->change();
            $table->string('state', 100)->change();
            $table->string('country', 100)->change();
            $table->string('latitude', 50)->change();
            $table->string('longitude', 50)->change();
            $table->string('azimuth', 50)->change();
            $table->string('elevation', 50)->change();
            $table->string('fov', 50)->change();
            $table->string('camera_model', 150)->change();
            $table->string('camera_lens', 150)->change();
            $table->string('camera_capture', 150)->change();
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

        Schema::table('stations', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('city')->change();
            $table->string('state')->change();
            $table->string('country')->change();
            $table->string('latitude')->change();
            $table->string('longitude')->change();
            $table->string('azimuth')->change();
            $table->string('elevation')->change();
            $table->string('fov')->change();
            $table->string('camera_model')->change();
            $table->string('camera_lens')->change();
            $table->string('camera_capture')->change();
        });
    }
}
