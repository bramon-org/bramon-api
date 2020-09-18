<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('captures', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('station_id');

            $table->string('capture_hash')->unique();

            $table->string('class')->nullable();

            $table->string('fs')->nullable();
            $table->string('fe')->nullable();
            $table->string('sec')->nullable();
            $table->string('av')->nullable();
            $table->string('mag')->nullable();

            $table->string('cdeg')->nullable();
            $table->string('cdegmax')->nullable();

            $table->string('av1')->nullable();
            $table->string('azm')->nullable();
            $table->string('evm')->nullable();

            $table->string('ra1')->nullable();
            $table->string('ra2')->nullable();
            $table->string('lat1')->nullable();
            $table->string('lat2')->nullable();
            $table->string('lng1')->nullable();
            $table->string('lng2')->nullable();

            $table->string('Vo')->nullable();

            $table->string('az1')->nullable();
            $table->string('az2')->nullable();
            $table->string('ev1')->nullable();
            $table->string('ev2')->nullable();
            $table->string('h1')->nullable();
            $table->string('h2')->nullable();
            $table->string('dist1')->nullable();
            $table->string('dist2')->nullable();

            $table->timestamp('captured_at');
            $table->timestamps();

            $table->primary('id');

            $table->index(['station_id', 'class']);

            $table->foreign('station_id')->references('id')->on('stations');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('captures');
    }
}
