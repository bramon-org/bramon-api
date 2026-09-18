<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropFilesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('files');
    }

    public function down()
    {
        if (!Schema::hasTable('files')) {
            Schema::create('files', function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('capture_id');
                $table->string('file_hash', 255)->unique();
                $table->string('filename', 255);
                $table->string('type', 50);
                $table->string('extension', 5);
                $table->string('url', 255)->nullable();
                $table->timestamp('captured_at');
                $table->timestamps();
                $table->primary('id');
                $table->index('capture_id');
                $table->index('filename');
                $table->foreign('capture_id')->references('id')->on('captures');
                $table->softDeletes();
            });
        }
    }
}