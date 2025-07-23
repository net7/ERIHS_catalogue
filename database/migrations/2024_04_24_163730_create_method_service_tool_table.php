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
        Schema::create('method_service_tool', function (Blueprint $table) {
            $table->id();

            $table->unsignedBiginteger('method_id')->unsigned();
            $table->unsignedBiginteger('service_id')->unsigned();
            $table->unsignedBiginteger('tool_id')->unsigned();

            $table->foreign('method_id')->references('id')
                ->on('methods')->onDelete('cascade');
            $table->foreign('service_id')->references('id')
                ->on('services')->onDelete('cascade');
            $table->foreign('tool_id')->references('id')
                ->on('tools')->onDelete('cascade');

            //TODO:aggiungere campi tool_role e tool_role_other provenienti dallo schema method/related_tool


            $table->unique(['method_id', 'service_id', 'tool_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('method_service_tool');
    }
};
