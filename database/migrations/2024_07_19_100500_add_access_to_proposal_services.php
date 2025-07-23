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
        Schema::table('proposal_service', function (Blueprint $table) {
            $table->enum('access',['scheduled','carried_out'])->default(null)->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_service', function (Blueprint $table) {
            $table->dropColumn(['access']);
        });
    }
};
