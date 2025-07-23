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
        Schema::create('proposal_evaluations', function (Blueprint $table) {
            $table->id();
            $table->integer('scientific_excellence');
            $table->integer('state_of_the_art_topic');
            $table->integer('valorization_and_dissemination_plan');
            $table->integer('expertise_of_user_group');
            $table->integer('potential_impact');
            $table->unsignedBiginteger('reviewer_id')->unsigned();
            $table->unsignedBiginteger('proposal_id')->unsigned();

            $table->foreign('proposal_id')->references('id')
                 ->on('proposals')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')
                ->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_evaluations');
    }
};
