<?php

use App\Enums\LearnedAboutErihs;
use App\Enums\ProposalConnection;
use App\Enums\ProposalEligibility;
use App\Enums\ProposalInternalStatus;
use App\Enums\ProposalSocialChallenges;
use App\Enums\ProposalStatus;
use App\Enums\ProposalType;
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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->drafts();
            $table->string('name')->nullable();
            $table->string('acronym')->nullable();
            $table->enum('type', ProposalType::names())->nullable();


            $table->mediumText('continuation_motivation')->nullable();
            $table->string('resubmission_previous_proposal_number')->nullable();
            $table->string('related_project')->nullable();
            // $table->integer('number_of_applicants')->nullable();
            $table->mediumText('cv')->nullable();

            $table->boolean('providers_contacted')->nullable();
            $table->boolean('facility_contacted')->nullable();
            //$table->string('community')->nullable(); // linked to .env('COMMUNITIES')

            $table->enum('internal_status', ProposalInternalStatus::names())->nullable();
            $table->enum('status', ProposalStatus::values())->nullable();

            //$table->integer('rating'); //TODO:rendere nullable

            $table->string('comment')->nullable();
            $table->string('scope_note')->nullable();
            $table->text('project_justification')->nullable();

            $table->boolean('eu_or_national_projects_related')->nullable();
            $table->text('name_of_the_project')->nullable();
            $table->text('founded_by')->nullable();
            $table->text('number_of_grant_agreement')->nullable();
            $table->boolean('training_activity')->nullable();
            $table->text('training_activity_details')->nullable();
            $table->boolean('industrial_involvement')->nullable();
            $table->text('industrial_involvement_details')->nullable();
            $table->enum('learned_about_erihs', LearnedAboutErihs::names())->nullable();
            $table->string('other_details')->nullable();

            $table->boolean('terms_and_conditions')->nullable();
            $table->boolean('consent_to_videotape_and_photography')->nullable();
            $table->boolean('news_via_email')->nullable();

            $table->mediumText('project_description')->nullable();
            $table->mediumText('project_summary')->nullable();
            $table->mediumText('scientific_background')->nullable();
            $table->mediumText('description_of_the_planned_work')->nullable();
            $table->mediumText('research_questions')->nullable();
            $table->mediumText('previous_analysis')->nullable();
            $table->mediumText('expected_achievements')->nullable();
            $table->mediumText('project_impacts')->nullable();
            $table->mediumText('data_management_plan')->nullable();
            $table->mediumText('references')->nullable();


            // $table->json('project_questions')->nullable();
            // $table->json('project_method_statements')->nullable();
            $table->string('erihs_id')->nullable();
            // $table->integer('version')->nullable();
            // $table->string('assessment')->nullable();
            $table->enum('eligibility', ProposalEligibility::names())->nullable(); //TODO
            $table->json('social_challenges')->nullable();
            // $table->enum('initiated_via', ProposalConnection::names())->nullable();
            // $table->string('related_training')->nullable();
            // $table->string('creator')->nullable();
            $table->string('whom')->nullable();

            // spostati in {molab,fixlab,archlab}_objects_data
            // $table->json('type_of_object')->nullable();
            // $table->string('location')->nullable();
            // $table->string('ownership')->nullable();
            // $table->json('ownership_consent')->nullable();
            // $table->json('drone_flight')->nullable();
            // $table->string('material')->nullable();
            // $table->string('quantity')->nullable();
            // $table->string('object_type')->nullable();
            // $table->string('size')->nullable();
            // $table->string('temperature')->nullable();
            // $table->string('environment_details')->nullable();
            // $table->string('preparation')->nullable();
            // $table->longText('other_notes')->nullable();
            $table->boolean('had_second_draft')->default(false);


            $table->string('archlab_type')->nullable();
            $table->text('archlab_type_other')->nullable();

            $table->integer('molab_quantity')->nullable();
            $table->json('molab_objects_data')->nullable();
            $table->string('molab_drone_flight')->nullable();
            $table->string('molab_drone_flight_file')->nullable();
            $table->text('molab_drone_flight_comment')->nullable();
            $table->text('molab_note')->nullable();
            $table->text('molab_logistic')->nullable();
            $table->boolean('molab_x_ray')->nullable();
            $table->string('molab_x_ray_file')->nullable();

            $table->integer('fixlab_quantity')->nullable();
            $table->json('fixlab_objects_data')->nullable();
            $table->text('fixlab_logistic')->nullable();


            $table->unsignedBiginteger('call_id')->unsigned();
            $table->foreign('call_id')->references('id')
                ->on('calls')->onDelete('cascade');

            //TODO selected_services
            //TODO project_attachments
            //TODO project_dmp
            //TODO participants

            /*

            // $table->string('object_size');
            // $table->text('object_location');
            // $table->text('object_ownership');

            // $table->enum('ownership_consent',  ProposalOwnershipConsent::names());

            // $table->string('ownership_consent_path');
            // $table->mediumText('ownership_consent_other_details');

            // $table->enum('drone_flight_authorizations', ProposalDroneFlightAuthorization::names());
            // $table->string('drone_flight_authorizations_path');

            // TODO: dates

            // $table->mediumText('logistics');
            // $table->string('project_description_path');

            // TODO: additional attachments

            // $table->boolean('iperion_related_projects')->default(false);
            // $table->mediumText('iperion_related_projects_description');
            // $table->boolean('eu_related_projects')->default(false);
            // $table->mediumText('eu_related_projects_description');

            // $table->boolean('video_consent');
            // $table->boolean('email_consent');
            */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
