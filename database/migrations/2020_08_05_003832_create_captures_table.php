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
            $table->uuid('user_id');
            $table->uuid('station_id');

            $table->string('class')->nullable();
            $table->string('mag')->nullable();
            $table->string('sec')->nullable();
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

            $table->json('files');

            $table->boolean('analyzed')->default(false);

            $table->timestamp('date');
            $table->timestamps();

            $table->primary('id');
            $table->index('user_id');
            $table->index('station_id');
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
