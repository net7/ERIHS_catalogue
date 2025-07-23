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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->mediumText('description');
            $table->mediumText('potential_results')->nullable();
            $table->date('last_checked_date');
            $table->json('url')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('calibration')->nullable();
            $table->unsignedBigInteger('organization_id')->index();
            $table->string('tool_type')->default('equipment');
            $table->string('developer')->nullable();
            $table->string('version')->nullable();
            $table->date('release_date')->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
