<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilesToCapturesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('captures', 'files')) {
            Schema::table('captures', function (Blueprint $table) {
                $table->json('files')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('captures', 'files')) {
            Schema::table('captures', function (Blueprint $table) {
                $table->dropColumn('files');
            });
        }
    }
}