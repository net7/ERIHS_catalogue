<?php

namespace App\Observers;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\ProposalEvaluation;
use App\ProposalStatusActivities\EvaluationStatusActivity;
use App\ProposalStatusActivities\FeasibilityResponseStatusActivity;
use App\Services\ERIHSMailService;

class ProposalEvaluationObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Proposal "created" event.
     */
    public function created(ProposalEvaluation $proposalEvaluation): void
    {
        // TODO:
        // - send an email to all the users with role Help Desk
        // - send an email to all the users with role instrumental_scientist related to tools involved in the proposal
        // - send an email to the user creating the proposal to confirm its creation
    }

    /**
     * Handle the ProposalEvaluation "updated" event.
     */
    public function updated(ProposalEvaluation $proposalEvaluation): void
    {

    }

    /**
     * Handle the ProposalEvaluation "deleted" event.
     */
    public function deleted(ProposalEvaluation $proposalEvaluation): void
    {
        //
    }

    /**
     * Handle the ProposalEvaluation "restored" event.
     */
    public function restored(ProposalEvaluation $proposalEvaluation): void
    {
        //
    }

    /**
     * Handle the ProposalEvaluation "force deleted" event.
     */
    public function forceDeleted(ProposalEvaluation $proposalEvaluation): void
    {
        //
    }

    public function saved(ProposalEvaluation $proposalEvaluation): void
    {

        $mailService = new ERIHSMailService();

        $proposal = $proposalEvaluation->proposal;
        if (!$proposal) {
            return;
        }

        if ($proposal->canBeRanked()) {
            if(count($proposal->evaluations()->get()) == 2) {
                $mailService->proposalEvaluationUpdate($proposal);
            }
            if(count($proposal->evaluations()->get()) == 3) {
                $mailService->thirdReviewerEvaluatedTheProposal($proposal);
            }
        }
        $statusActivity = new EvaluationStatusActivity($proposalEvaluation->toArray());
        $proposal->addActivityAndSave($statusActivity);

        return;

    }
}
