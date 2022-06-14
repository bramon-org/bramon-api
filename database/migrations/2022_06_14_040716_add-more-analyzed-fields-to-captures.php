<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreAnalyzedFieldsToCaptures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('captures', function (Blueprint $table) {
            $table->string('dec1', 15)->nullable();
            $table->string('dec2', 15)->nullable();
            $table->string('alt', 15)->nullable();
            $table->string('cx', 15)->nullable();
            $table->string('cy', 15)->nullable();
            $table->string('fps', 15)->nullable();
            $table->string('frames', 15)->nullable();
            $table->string('rotation', 15)->nullable();
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
            $table->dropColumn('dec1');
            $table->dropColumn('dec2');
            $table->dropColumn('alt');
            $table->dropColumn('cx');
            $table->dropColumn('cy');
            $table->dropColumn('fps');
            $table->dropColumn('frames');
            $table->dropColumn('rotation');
        });
    }
}
