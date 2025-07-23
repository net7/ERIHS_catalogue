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
        Schema::table('proposal_attachments', function (Blueprint $table) {
            $table->text('caption')->nullable();
            $table->string('original_file_name')->nullable();
            $table->unsignedBigInteger('proposal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_attachments', function (Blueprint $table) {
            $table->dropColumn(['caption']);
            $table->dropColumn(['proposal_id']);
            $table->dropColumn(['original_file_name']);
        });
    }
};
