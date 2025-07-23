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
        Schema::create('proposal_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proposal_id')->index();
            $table->foreign('proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            $table->unsignedBigInteger('service_id')->index();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->date('first_choice_start_date')->nullable();
            $table->date('first_choice_end_date')->nullable();
            $table->date('second_choice_start_date')->nullable();
            $table->date('second_choice_end_date')->nullable();
            $table->string('number_of_days')->nullable();
            $table->enum('feasible', ['feasible', 'not_feasible', 'to_be_defined'])->default('to_be_defined');
            $table->mediumText('unfeasibility_motivation')->nullable();
            $table->timestamps();
            $table->unique(['proposal_id', 'service_id'],'u_propserv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_service');
    }
};
