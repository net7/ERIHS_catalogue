<?php

namespace App\Listeners;

use App\Enums\ProposalStatus;
use App\Http\Traits\ActivityLogTrait;
use App\Models\PostAccessReport;
use App\Services\ERIHSMailService;
use App\Services\ProposalService;
use Gecche\FSM\Events\StatusTransitionDone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HandleProposalStatusTransition
{
    use ActivityLogTrait;

    protected $model;
    protected $prevStatusCode;
    protected $statusCode;
    protected $statusData;
    protected $saved;
    protected $params;

    protected $mailService;

    public function handle(StatusTransitionDone $event)
    {

        $this->model = $event->model;
        $this->prevStatusCode = $event->prevStatusCode;
        $this->statusCode = $event->statusCode;
        $this->statusData = $event->statusData;
        $this->saved = $event->saved;
        $this->params = $event->params;

        $this->mailService = resolve(ERIHSMailService::class);

        $methodName = 'handleTransitionFrom' . Str::studly($event->prevStatusCode) . 'To' . Str::studly($event->statusCode);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        $this->handleTransition();
    }


    protected function handleTransitionFromDraftToSubmitted()
    {

        $this->mailService->applicationSubmitted(Auth::user(), $this->model);
    }

    protected function handleTransitionFromSecondDraftToResubmitted()
    {
        $this->model->resetProposalServices();
        $this->mailService->applicationResubmitted(Auth::user(), $this->model);
    }


    protected function handleTransitionFromFeasibleToSystemReviewersChosen()
    {
        Log::debug('In handleTransitionFromFeasibleToSystemReviewersChosen');
        // $this->moveToReviewersChosenState();
    }


    // // protected function moveToReviewersChosenState()
    // protected function setStatusSystemReviewersChosen()
    // {
    //     // if ($this->model->status != ProposalStatus::SYSTEM_REVIEWERS_CHOSEN->value){
    //     // Log::debug('In moveToReviewersChosenState');

    //     ProposalService::assignReviewers($this->model->getKey());
    //     // $this->model->makeTransitionAndSave(ProposalStatus::SYSTEM_REVIEWERS_CHOSEN->value);
    //     // }
    // }

    protected function makeFeasible()
    {
        Log::debug('In makeFeasible');

        $this->mailService->applicationFeasible(Auth::user(), $this->model);
        if ($this->model->call->isClosed()) {
            $this->model->makeTransitionAndSave(ProposalStatus::SYSTEM_REVIEWERS_CHOSEN->value);
        }
    }

    /*
     * FIRST ROUND OF FEASIBILITY
     */
    protected function handleTransitionFromSubmittedToFeasible()
    {

        $this->makeFeasible();
    }
    protected function handleTransitionFromSubmittedToNotFeasible()
    {

        $this->mailService->applicationNotFeasible(Auth::user(), $this->model);

        $this->model->had_second_draft = true;
        $this->model->makeTransitionAndSave(ProposalStatus::SECOND_DRAFT->value);
    }
    protected function handleTransitionFromSubmittedToPartiallyFeasible()
    {

        $this->mailService->applicationPartiallyFeasible(Auth::user(), $this->model);

        //$this->model->resetProposalServices();
        $this->model->had_second_draft = true;
        $this->model->makeTransitionAndSave(ProposalStatus::SECOND_DRAFT->value);
    }


    /*
     * SECOND ROUND OF FEASIBILITY
     */

    protected function handleTransitionFromResubmittedToFeasible()
    {

        $this->makeFeasible();
    }
    protected function handleTransitionFromResubmittedToSecondNotFeasible()
    {

        $this->mailService->applicationNotFeasible(Auth::user(), $this->model);
        $this->model->makeTransitionAndSave(ProposalStatus::ARCHIVED->value);
    }
    protected function handleTransitionFromResubmittedToSecondPartiallyFeasible()
    {

        $this->mailService->applicationPartiallyFeasible(Auth::user(), $this->model);
        $this->model->makeTransitionAndSave(ProposalStatus::ARCHIVED->value);
    }

    protected function handleTransitionFromSystemReviewersChosenToRankedReserveList()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationInReserveList(Auth::user(), $this->model);
    }

    protected function handleTransitionFromSystemReviewersChosenToRankedMainList()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationInMainList(Auth::user(), $this->model);
    }

    protected function handleTransitionFromSystemReviewersChosenToRankedBelowThreshold()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationRejected(Auth::user(), $this->model);
        $this->model->makeTransitionAndSave(ProposalStatus::ARCHIVED->value);
    }

    protected function handleTransitionFromFeasibleToRankedMainList()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationInMainList(Auth::user(), $this->model);
    }

    protected function handleTransitionFromFeasibleToRankedReserveList()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationInReserveList(Auth::user(), $this->model);
    }

    protected function handleTransitionFromFeasibleToRankedBelowThreshold()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationRejected(Auth::user(), $this->model);
        $this->model->makeTransitionAndSave(ProposalStatus::ARCHIVED->value);
    }

    protected function handleTransitionFromUnderReviewToRankedMainList()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationInMainList(Auth::user(), $this->model);
    }

    protected function handleTransitionFromUnderReviewToRankedReserveList()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationInReserveList(Auth::user(), $this->model);
    }

    protected function handleTransitionFromUnderReviewToRankedBelowThreshold()
    {
        // block access to pending reviewers
        ProposalService::blockReviewersAfterRanking($this->model->getKey());

        $this->mailService->applicationRejected(Auth::user(), $this->model);
        $this->model->makeTransitionAndSave(ProposalStatus::ARCHIVED->value);
    }

    protected function handleTransitionFromRankedReserveListToRankedMainList()
    {

        $this->mailService->applicationInMainList(Auth::user(), $this->model);
    }

    protected function handleTransitionFromRankedMainListToAccepted()
    {

        $this->mailService->applicationAccepted(Auth::user(), $this->model);
    }

    protected function handleTransitionFromAcceptedToAccessClosed()
    {

        $this->mailService->closeProposal($this->model);
        PostAccessReport::create([
            'user_id' => $this->model->getLeaderId(),
            'proposal_id' => $this->model->getKey(),
            'summary' => '',
        ]);
    }

    /*
     * @return void
     */
    protected function handleTransition()
    {
        $fsm = $this->model->getFsm();
        $rootState = $fsm->getRootState();
        switch ($this->statusCode) {

            case $rootState:
                break;
            case 'SUBMITTED':
                activity()
                    ->performedOn($this->model)
                    ->withProperties($this->getItemLogProperties($this->model, 'proposal'))
                    ->log($this->getItemLogString($this->model, 'proposal'));
                break;
            case 'FEASIBLE':
                break;
            case 'SYSTEM_REVIEWERS_CHOSEN':
                break;
            case 'UH_REVIEWERS_MANAGING':
                break;
            case 'REVIEWERS_CHANGED':
                break;
            case 'REVIEWERS_CONFLICT':
                break;
            case 'UNDER_REVIEW':
                break;
            case 'PROPOSAL_RANKED':
                break;
            case 'PROPOSAL_IN_RESERVE_LIST':
                break;
            case 'PROPOSAL_ARCHIVED':
                break;
            case 'PROPOSAL_OFFER_MADE':
                break;
            case 'PROPOSAL_ACCESS_CARRIED_OUT':
                break;
            default:
                break;
        }
    }
}
