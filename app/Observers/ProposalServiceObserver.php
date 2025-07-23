<?php

namespace App\Observers;

use App\Enums\ProposalStatus;
use App\Models\ProposalService;
use App\ProposalStatusActivities\AccessCarriedOutStatusActivity;
use App\ProposalStatusActivities\AccessScheduledStatusActivity;
use App\ProposalStatusActivities\FeasibilityResponseStatusActivity;
use App\Services\ERIHSMailService;

class ProposalServiceObserver
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
    public function created(ProposalService $proposalService): void
    {
        // TODO:
        // - send an email to all the users with role Help Desk
        // - send an email to all the users with role instrumental_scientist related to tools involved in the proposal
        // - send an email to the user creating the proposal to confirm its creation
    }

    /**
     * Handle the ProposalService "updated" event.
     */
    public function updated(ProposalService $proposalService): void
    {
        //
    }

    /**
     * Handle the ProposalService "deleted" event.
     */
    public function deleted(ProposalService $proposalService): void
    {
        //
    }

    /**
     * Handle the ProposalService "restored" event.
     */
    public function restored(ProposalService $proposalService): void
    {
        //
    }

    /**
     * Handle the ProposalService "force deleted" event.
     */
    public function forceDeleted(ProposalService $proposalService): void
    {
        //
    }

    public function saved(ProposalService $proposalService): void
    {

        $proposal = $proposalService->proposal;

        if (!$proposal) {
            return;
        }

        //Solo se stato è submitted o resubmitted
        if ($proposalService->proposal->status == ProposalStatus::FILES_CONFIRMED->value) {
            if ($proposalService->access == ProposalService::ACCESS_SCHEDULED) {
                $statusActivity = new AccessScheduledStatusActivity($proposalService->toArray());
                $proposal->addActivityAndSave($statusActivity);
                if ($proposal->isAccessScheduled()) {
                    $proposal->makeTransitionAndSave(ProposalStatus::ACCESS_SCHEDULED->value);
                }
                return;
            }
        }
        if (
            $proposalService->proposal->status == ProposalStatus::ACCESS_SCHEDULED->value ||
            $proposalService->proposal->status == ProposalStatus::FILES_CONFIRMED->value
        ) {
            if ($proposalService->access == ProposalService::ACCESS_CARRIED_OUT) {
                $statusActivity = new AccessCarriedOutStatusActivity($proposalService->toArray());
                $proposal->addActivityAndSave($statusActivity);

                if ($proposal->isCarriedOut()) {
                    $proposal->makeTransitionAndSave(ProposalStatus::ACCESS_CLOSED->value);
                    (new ERIHSMailService())->closeProposal($proposal);
                }
                return;
            }
        }

        //Solo se stato è submitted o resubmitted
        if (
            $proposalService->proposal->status != ProposalStatus::SUBMITTED->value &&
            $proposalService->proposal->status != ProposalStatus::RESUBMITTED->value
        ) {
            return;
        }

        if (!$proposalService->hasFeasibility()) {
            return;
        }

        $statusActivity = new FeasibilityResponseStatusActivity($proposalService->toArray());
        $proposal->addActivityAndSave($statusActivity);

        $feasibilityResponse = $proposal->feasibilityResponse();
        if ($feasibilityResponse) {
            $proposal->makeTransitionAndSave($feasibilityResponse->value);
        }
    }
}
