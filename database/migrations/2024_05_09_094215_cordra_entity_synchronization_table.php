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
        Schema::create('cordra_entity_synchronization', function (Blueprint $table) {
            $table->id();

            $table->string('entity_id')->nullable();
            $table->string('entity_type')->nullable();
            $table->string('cordra_id')->nullable();
            $table->boolean('synchronized')->default(false)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cordra_entity_synchronization');
    }
};
