<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('acronym');
            $table->string('name');
            $table->json('external_pid')->nullable();
            $table->string('mbox'); //email
            $table->string('phone')->nullable();
            $table->string('img_url')->nullable();
            $table->json('webpages')->nullable();
            $table->json('research_references')->nullable();
            $table->date('joined_the_field_date')->nullable();
            $table->date('first_active_date')->nullable();
            $table->date('last_active_date')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
