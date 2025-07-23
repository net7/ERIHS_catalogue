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
        Schema::create('applicant_proposal', function (Blueprint $table) {
            $table->boolean('leader')->default(false);
            $table->boolean('alias')->default(false);
            $table->unsignedBigInteger('applicant_id')->index();
            $table->foreign('applicant_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('proposal_id')->index();
            $table->foreign('proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            $table->primary(['applicant_id', 'proposal_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_proposal');
    }
};
