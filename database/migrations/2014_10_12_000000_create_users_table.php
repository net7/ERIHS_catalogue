<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->foreignId('current_connected_account_id')->nullable();
            $table->text('profile_photo_path')->nullable();
            $table->boolean('terms_of_service')->default(false);
            $table->boolean('confidentiality')->default(false);
            $table->json('disciplines')->nullable();
            $table->integer('number_of_reviews')->nullable()->default(null);
            $table->string('api_token')->nullable();
            $table->json('object_types')->nullable();

            $table->timestamps();
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
