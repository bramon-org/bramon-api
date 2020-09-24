<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactoryCapturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('captures', function (Blueprint $table) {
            $table->string('capture_hash', 255)->change();
            $table->string('class', 10)->change();
            $table->string('fs', 2)->change();
            $table->string('fe', 2)->change();
            $table->string('sec', 15)->change();
            $table->string('av', 15)->change();
            $table->string('mag', 15)->change();
            $table->string('cdeg', 15)->change();
            $table->string('cdegmax', 15)->change();
            $table->string('av1', 15)->change();
            $table->string('azm', 15)->change();
            $table->string('evm', 15)->change();
            $table->string('ra1', 15)->change();
            $table->string('ra2', 15)->change();
            $table->string('lat1', 15)->change();
            $table->string('lat2', 15)->change();
            $table->string('lng1', 15)->change();
            $table->string('lng2', 15)->change();
            $table->string('Vo', 15)->change();
            $table->string('az1', 15)->change();
            $table->string('az2', 15)->change();
            $table->string('ev1', 15)->change();
            $table->string('ev2', 15)->change();
            $table->string('h1', 15)->change();
            $table->string('h2', 15)->change();
            $table->string('dist1', 15)->change();
            $table->string('dist2', 15)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('captures', function (Blueprint $table) {
            $table->string('capture_hash', 200)->change();
            $table->string('class', 10)->change();
            $table->string('fs', 2)->change();
            $table->string('fe', 2)->change();
            $table->string('sec')->change();
            $table->string('av')->change();
            $table->string('mag')->change();
            $table->string('cdeg')->change();
            $table->string('cdegmax')->change();
            $table->string('av1')->change();
            $table->string('azm')->change();
            $table->string('evm')->change();
            $table->string('ra1')->change();
            $table->string('ra2')->change();
            $table->string('lat1')->change();
            $table->string('lat2')->change();
            $table->string('lng1')->change();
            $table->string('lng2')->change();
            $table->string('Vo')->change();
            $table->string('az1')->change();
            $table->string('az2')->change();
            $table->string('ev1')->change();
            $table->string('ev2')->change();
            $table->string('h1')->change();
            $table->string('h2')->change();
            $table->string('dist1')->change();
            $table->string('dist2')->change();
        });
    }
}
