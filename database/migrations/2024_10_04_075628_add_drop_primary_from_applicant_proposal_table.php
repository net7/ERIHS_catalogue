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
        Schema::table( 'applicant_proposal', function (Blueprint $table) {
            $table->dropPrimary(['applicant_id', 'proposal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_proposal', function (Blueprint $table) {
            $table->primary(['applicant_id', 'proposal_id']);
        });
    }
};
