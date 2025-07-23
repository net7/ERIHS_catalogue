<?php

use App\Enums\ProposalReviewerRefusalReason;
use App\Enums\ProposalReviewerStatus;
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
        Schema::create('proposal_reviewer', function (Blueprint $table) {
            $table->id();

            //rewiever_id	proposal_id	status	refused_reason	refused_comment	accepted_at	refused_at
            $table->unsignedBiginteger('reviewer_id')->unsigned();
            $table->unsignedBiginteger('proposal_id')->unsigned();
            $table->enum('status', ProposalReviewerStatus::names())->nullable();
            $table->enum('refused_reason', ProposalReviewerRefusalReason::names())->nullable();
            $table->longText('refused_comment')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('refused_at')->nullable();
            
            $table->foreign('reviewer_id')->references('id')
                 ->on('users')->onDelete('cascade');
            $table->foreign('proposal_id')->references('id')
                ->on('proposals')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_reviewers');
    }
};
