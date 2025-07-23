<?php

namespace App\Observers;

use App\Enums\ProposalReviewerRefusalReason;
use App\Enums\ProposalReviewerStatus;
use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\ProposalStatusActivities\FeasibilityResponseStatusActivity;
use App\ProposalStatusActivities\ReviewerAcceptanceStatusActivity;
use App\ProposalStatusActivities\ReviewerCancellationStatusActivity;
use App\ProposalStatusActivities\ReviewerRejectionStatusActivity;
use App\ProposalStatusActivities\ReviewerSelectionStatusActivity;
use App\Services\ERIHSMailService;

class ProposalReviewerObserver
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
    public function created(ProposalReviewer $proposalReviewer): void
    {
        // TODO:
        // - send an email to all the users with role Help Desk
        // - send an email to all the users with role instrumental_scientist related to tools involved in the proposal
        // - send an email to the user creating the proposal to confirm its creation
    }

    /**
     * Handle the ProposalReviewer "updated" event.
     */
    public function updated(ProposalReviewer $proposalReviewer): void
    {
        //

        $mailService = new ERIHSMailService();

        $proposal = $proposalReviewer->proposal;
        if (!$proposal) {
            return;
        }

        switch ($proposalReviewer->status) {
            case ProposalReviewerStatus::WAITING->name:
//                if ($proposal->isFirstReviewerConfirmed() && $proposal->status == ProposalStatus::SYSTEM_REVIEWERS_CHOSEN->value) {
//                    $proposal->makeTransition(ProposalStatus::UH_REVIEWERS_MANAGING->value);
//                }
                $mailService->selectReviewer($proposalReviewer->reviewer, $proposal);
                $statusActivity = new ReviewerSelectionStatusActivity($proposalReviewer->toArray());
                $proposal->addActivityAndSave($statusActivity);
                return;
            case ProposalReviewerStatus::REFUSED->name:
                $reason = $proposalReviewer->refused_reason;
                switch ($reason) {
                    case ProposalReviewerRefusalReason::CONFLICT_OF_INTEREST->name:
                        $mailService->reviewerConflicts($proposalReviewer->reviewer, $proposal);
                        break;
                    case ProposalReviewerRefusalReason::TIMED_OUT->name:
                        $mailService->newReviewerSelection($proposalReviewer->reviewer, $proposal);
                        break;
                    case ProposalReviewerRefusalReason::EXPLICIT_REFUSAL->name:
                        $mailService->reviewerExplicitRefusal($proposalReviewer->reviewer, $proposal);
                        break;
                }
                $statusActivity = new ReviewerRejectionStatusActivity($proposalReviewer->toArray());
                $proposal->addActivityAndSave($statusActivity);
                return;
            case ProposalReviewerStatus::ACCEPTED->name:
                $mailService->reviewerAcceptance($proposalReviewer->reviewer, $proposal);
                $statusActivity = new ReviewerAcceptanceStatusActivity($proposalReviewer->toArray());
                $proposal->addActivityAndSave($statusActivity);
                $reviewersAcceptanceResponse = $proposal->reviewersAcceptanceResponse();
                if ($reviewersAcceptanceResponse) {
                    $proposal->makeTransitionAndSave(ProposalStatus::UNDER_REVIEW->value);
                }
                return;
            default:
                return;

        }
    }

    /**
     * Handle the ProposalReviewer "deleted" event.
     */
    public function deleted(ProposalReviewer $proposalReviewer): void
    {
        //
        $mailService = new ERIHSMailService();

        $proposal = $proposalReviewer->proposal;
        if (!$proposal) {
            return;
        }

        if ($proposalReviewer->status == ProposalReviewerStatus::TO_BE_CONFIRMED->name) {
            return;
        }

        $mailService->reviewerDeleted($proposalReviewer->reviewer, $proposal);
        $statusActivity = new ReviewerCancellationStatusActivity($proposalReviewer->toArray());
        $proposal->addActivityAndSave($statusActivity);
        return;
    }

    /**
     * Handle the ProposalReviewer "restored" event.
     */
    public function restored(ProposalReviewer $proposalReviewer): void
    {
        //
    }

    /**
     * Handle the ProposalReviewer "force deleted" event.
     */
    public function forceDeleted(ProposalReviewer $proposalReviewer): void
    {
        //
    }

    public function saved(ProposalReviewer $proposalReviewer): void
    {


    }
}
