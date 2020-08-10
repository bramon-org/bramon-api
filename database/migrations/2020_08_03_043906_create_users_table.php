<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile_phone')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('api_token');
            $table->string('last_request_ip', 15)->nullable();

            $table->enum('role', User::AVAILABLE_ROLES)->default(User::ROLE_OPERATOR);

            $table->boolean('public')->default(true);
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->primary('id');

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
        Schema::dropIfExists('users');
    }
}
