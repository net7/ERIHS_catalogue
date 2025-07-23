<?php

namespace App\Console\Commands;

use App\Enums\ProposalStatus;
use App\Services\CallService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessCallsClosure extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-calls-closure';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carries out the reviewers assignement when a call ends';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        $endedCalls = CallService::getClosedCalls();

        if (!$endedCalls) {
            Log::info('No ended calls, quitting');
            return;
        }
        foreach ($endedCalls as $call) {

            Log::info('Closing call ' . $call->id);
            if ($call->isClosed() && !$call->isProcessed()) {

                foreach ($call->proposals as $proposal) {
                    // advance all proposals in the call if in the correct states
                    if ($proposal->status == ProposalStatus::FEASIBLE->value) {
                        $proposal->makeTransitionAndSave(ProposalStatus::SYSTEM_REVIEWERS_CHOSEN->value);
                    }
                }

                if (CallService::haveAllProposalsBeenEvaluatedForFeasibility($call)) {
                    $call->setIsProcessed();
                }
            } else {
                Log::info('Call ' . $call->id . ' was already closed and processed');
            }
        }
    }
}
