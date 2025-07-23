<?php

namespace App\Observers;

use App\Models\Proposal;

class ProposalObserver
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
    public function created(Proposal $proposal): void
    {
        // TODO:
        // - send an email to all the users with role Help Desk
        // - send an email to all the users with role instrumental_scientist related to tools involved in the proposal
        // - send an email to the user creating the proposal to confirm its creation
    }

    /**
     * Handle the Proposal "updated" event.
     */
    public function updated(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "deleted" event.
     */
    public function deleted(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "restored" event.
     */
    public function restored(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "force deleted" event.
     */
    public function forceDeleted(Proposal $proposal): void
    {
        //
    }
}
