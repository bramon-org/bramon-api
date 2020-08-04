<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->string('name')->unique();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('azimuth');
            $table->string('elevation');
            $table->string('fov');
            $table->string('camera_model');
            $table->string('camera_lens');
            $table->string('camera_capture');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->primary('id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stations');
    }
}
