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
        Schema::table( 'method_service_tool', function (Blueprint $table) {
            $table->unsignedBiginteger('tool_id')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table( 'method_service_tool', function (Blueprint $table) {
            $table->unsignedBiginteger('tool_id')->change()->required();
        });
    }
};
