<?php

namespace App\Observers;

use App\Enums\ProposalStatus;
use App\Models\PostAccessReport;
use App\Services\ERIHSMailService;

class PostAccessReportObserver
{
    /**
     * Handle the PostAccessReport "created" event.
     */
    public function created(PostAccessReport $postAccessReport): void
    {
        $proposal = $postAccessReport->proposal;
        (new ERIHSMailService())->closeProcess($proposal);
        $proposal->makeTransitionAndSave(ProposalStatus::POST_ACCESS_DUTIES_DONE->value);
        $proposal->makeTransitionAndSave(ProposalStatus::ARCHIVED->value);
    }

    /**
     * Handle the PostAccessReport "updated" event.
     */
    public function updated(PostAccessReport $postAccessReport): void
    {
        //
    }

    /**
     * Handle the PostAccessReport "deleted" event.
     */
    public function deleted(PostAccessReport $postAccessReport): void
    {
        //
    }

    /**
     * Handle the PostAccessReport "restored" event.
     */
    public function restored(PostAccessReport $postAccessReport): void
    {
        //
    }

    /**
     * Handle the PostAccessReport "force deleted" event.
     */
    public function forceDeleted(PostAccessReport $postAccessReport): void
    {
        //
    }
}
