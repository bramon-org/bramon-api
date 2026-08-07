<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeohashToStationsAndCaptures extends Migration
{
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            if (!Schema::hasColumn('stations', 'geohash')) {
                $table->string('geohash', 12)->nullable()->after('longitude');
                $table->index('geohash');
            }
            if (!Schema::hasColumn('stations', 'latitude')) {
                // nothing
            }
            if (!Schema::hasColumn('stations', 'longitude')) {
                // nothing
            }
        });

        Schema::table('captures', function (Blueprint $table) {
            if (!Schema::hasColumn('captures', 'geohash')) {
                $table->string('geohash', 12)->nullable()->after('lng2');
                $table->index('geohash');
            }
            if (!Schema::hasColumn('captures', 'captured_at')) {
                // nothing
            }
        });

        // add index on captures.captured_at for faster time range queries
        Schema::table('captures', function (Blueprint $table) {
            $table->index('captured_at');
        });

        // add indexes on stations latitude/longitude if not exists
        Schema::table('stations', function (Blueprint $table) {
            $table->index('latitude');
            $table->index('longitude');
        });
    }

    public function down()
    {
        Schema::table('stations', function (Blueprint $table) {
            if (Schema::hasColumn('stations', 'geohash')) {
                $table->dropIndex(['geohash']);
                $table->dropColumn('geohash');
            }
            $table->dropIndex(['latitude']);
            $table->dropIndex(['longitude']);
        });

        Schema::table('captures', function (Blueprint $table) {
            if (Schema::hasColumn('captures', 'geohash')) {
                $table->dropIndex(['geohash']);
                $table->dropColumn('geohash');
            }
            $table->dropIndex(['captured_at']);
        });
    }
}
