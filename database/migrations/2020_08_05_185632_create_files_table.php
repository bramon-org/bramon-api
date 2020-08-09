<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('capture_id');

            $table->string('filename');
            $table->string('type');
            $table->string('extension');
            $table->string('url')->nullable();

            $table->timestamp('captured_at');
            $table->timestamps();

            $table->primary('id');

            $table->index('capture_id');
            $table->index('filename');

            $table->unique(['capture_id', 'filename', 'captured_at']);

            $table->foreign('capture_id')->references('id')->on('captures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
