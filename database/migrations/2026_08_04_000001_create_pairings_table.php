<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePairingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pairings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('capture_a_id');
            $table->uuid('capture_b_id');
            $table->integer('time_difference_seconds');
            $table->double('distance_km', 8, 3);
            $table->double('azimuth_diff')->nullable();
            $table->double('elevation_diff')->nullable();
            $table->double('fov_diff')->nullable();
            $table->date('pairing_date')->nullable();
            $table->timestamps();

            $table->index(['pairing_date']);
            $table->index(['capture_a_id']);
            $table->index(['capture_b_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pairings');
    }
}
