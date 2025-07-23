<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('methods', function (Blueprint $table) {
            $table->id();
            $table->string('preferred_label');
            $table->json('alternative_labels')->nullable();
            $table->string('method_type')->nullable();
            $table->string('technique_other')->nullable();
            $table->string('method_version');
            $table->mediumText('method_documentation')->nullable();
            $table->json('method_parameter')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('organization_id')->index();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methods');
    }
};
