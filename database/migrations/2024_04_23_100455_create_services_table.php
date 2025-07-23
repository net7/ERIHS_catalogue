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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->mediumText('summary');
            $table->mediumText('description');
            $table->boolean('application_required');

            //organizations
            $table->unsignedBigInteger('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

            $table->json('contacts')->nullable();
            $table->json('categories');
            $table->json('functions');

            $table->json('measurable_properties')->nullable();
            $table->mediumText('limitations')->nullable();
            $table->json('links')->nullable();
            $table->string('version')->nullable();
            $table->date('version_date')->nullable();
            $table->date('creation_date')->nullable();
            //$table->date('checked_date')->nullable();
            $table->mediumText('output_description')->nullable();
            $table->mediumText('input_description')->nullable();
            $table->mediumText('further_comments')->nullable();
            $table->integer('hours_per_unit')->nullable();
            $table->integer('access_unit_cost')->nullable();
            $table->boolean('service_active');
            $table->string('url')->nullable();

            $table->unsignedBigInteger('service_manager_id')->nullable();
            $table->foreign('service_manager_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('second_service_manager_id')->nullable();
            $table->foreign('second_service_manager_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
