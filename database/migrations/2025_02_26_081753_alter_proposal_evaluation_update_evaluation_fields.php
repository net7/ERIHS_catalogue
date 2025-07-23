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
        Schema::table('proposal_evaluations', function ($table) {

            // Remove old fields
            $table->dropColumn('scientific_excellence');
            $table->dropColumn('state_of_the_art_topic');
            $table->dropColumn('valorization_and_dissemination_plan');
            $table->dropColumn('expertise_of_user_group');
            $table->dropColumn('potential_impact');

            // Excellence
            $table->integer('excellence_relevance');
            $table->integer('excellence_methodology');
            $table->integer('excellence_originality');
            $table->integer('excellence_expertise');
            $table->integer('excellence_timeliness');
            $table->integer('excellence_state_of_the_art');

            // Impact
            $table->integer('impact_research');
            $table->integer('impact_knowledge_sharing');
            $table->integer('impact_innovation_potential');
            $table->integer('impact_open_access');
            $table->integer('impact_expected_impacts');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_evaluations', function ($table) {
            // Restore old fields
            $table->integer('scientific_excellence');
            $table->integer('state_of_the_art_topic');
            $table->integer('valorization_and_dissemination_plan');
            $table->integer('expertise_of_user_group');
            $table->integer('potential_impact');

            // Excellence
            $table->dropColumn('excellence_relevance');
            $table->dropColumn('excellence_methodology');
            $table->dropColumn('excellence_originality');
            $table->dropColumn('excellence_expertise');
            $table->dropColumn('excellence_timeliness');
            $table->dropColumn('excellence_state_of_the_art');

            // Impact
            $table->dropColumn('impact_research');
            $table->dropColumn('impact_knowledge_sharing');
            $table->dropColumn('impact_innovation_potential');
            $table->dropColumn('impact_open_access');
            $table->dropColumn('impact_expected_impacts');
        });
    }
};
