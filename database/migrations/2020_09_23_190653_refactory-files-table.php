<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactoryFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('file_hash', 255)->change();
            $table->string('filename', 255)->change();
            $table->string('type', 50)->change();
            $table->string('extension', 5)->change();
            $table->string('url', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('file_hash')->change();
            $table->string('filename')->change();
            $table->string('type')->change();
            $table->string('extension')->change();
            $table->string('url')->change();
        });
    }
}
