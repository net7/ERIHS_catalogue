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

        Schema::table('tools', function ($table) {
            $table->unsignedBigInteger('organization_id')->change()->nullable();
            $table->dropForeign('tools_organization_id_foreign');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null');
        });

        Schema::table('proposals', function ($table) {
            $table->unsignedBigInteger('call_id')->change()->nullable();
            $table->dropForeign('proposals_call_id_foreign');
            $table->foreign('call_id')
                ->references('id')
                ->on('calls')
                ->onDelete('set null');
        });

        Schema::table('methods', function ($table) {
            $table->unsignedBigInteger('organization_id')->change()->nullable();
            $table->dropForeign('methods_organization_id_foreign');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null');
        });

        Schema::table('services', function ($table) {
            $table->unsignedBigInteger('organization_id')->change()->nullable();
            $table->dropForeign('services_organization_id_foreign');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null');

            $table->unsignedBigInteger('service_manager_id')->change()->nullable();
            $table->dropForeign('services_service_manager_id_foreign');
            $table->foreign('service_manager_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->unsignedBigInteger('second_service_manager_id')->change()->nullable();
            $table->dropForeign('services_second_service_manager_id_foreign');
            $table->foreign('second_service_manager_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Schema::table('method_service_tool', function ($table) {
            $table->unsignedBigInteger('service_id')->change()->nullable();
            $table->dropForeign('method_service_tool_service_id_foreign');
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('set null');

            $table->unsignedBigInteger('method_id')->change()->nullable();
            $table->dropForeign('method_service_tool_method_id_foreign');
            $table->foreign('method_id')
                ->references('id')
                ->on('methods')
                ->onDelete('set null');

            $table->unsignedBigInteger('tool_id')->change()->nullable();
            $table->dropForeign('method_service_tool_tool_id_foreign');
            $table->foreign('tool_id')
                ->references('id')
                ->on('tools')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function ($table) {
            $table->dropForeign('tools_organization_id_foreign');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
        });

        Schema::table('proposals', function ($table) {
            $table->dropForeign('proposals_call_id_foreign');
            $table->foreign('call_id')
                ->references('id')
                ->on('calls')
                ->onDelete('cascade');
        });

        Schema::table( 'methods', function ($table) {
            $table->dropForeign('methods_organization_id_foreign');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
        });

        Schema::table( 'services', function ($table) {
            $table->dropForeign('services_organization_id_foreign');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->dropForeign('services_service_manager_id_foreign');
            $table->foreign('service_manager_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->dropForeign('services_second_service_manager_id_foreign');
            $table->foreign('second_service_manager_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table( 'method_service_tool', function ($table) {
            $table->dropForeign('method_service_tool_method_id_foreign');
            $table->foreign('method_id')
                ->references('id')
                ->on('methods')
                ->onDelete('cascade');

            $table->dropForeign('method_service_tool_tool_id_foreign');
            $table->foreign('tool_id')
                ->references('id')
                ->on('tools')
                ->onDelete('cascade');

            $table->dropForeign('method_service_tool_service_id_foreign');
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');
        });

    }
};
