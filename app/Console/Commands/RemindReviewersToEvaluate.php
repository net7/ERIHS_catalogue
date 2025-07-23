<?php

namespace App\Console\Commands;

use App\Enums\ProposalReviewerStatus;
use App\Models\Proposal;
use App\Models\ProposalEvaluation;
use App\Models\ProposalReviewer;
use App\Models\User;
use App\Services\ERIHSMailService;
use Illuminate\Console\Command;

class RemindReviewersToEvaluate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weekly-reminder {proposalID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent an email to remind the reviewers to evaluate the proposal and click on the button "evaluation made".';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $res = [];
        $proposalEvaluations = ProposalEvaluation::query()
            ->where('proposal_id', '=', $this->argument('proposalID'))
            ->get()
            ->pluck('reviewer_id')
            ->toArray();
        $proposalReviewers = ProposalReviewer::query()
            ->where('status', ProposalReviewerStatus::ACCEPTED)
            ->where('proposal_id', $this->argument('proposalID'))
            ->get()
            ->pluck('reviewer_id');
        foreach ($proposalReviewers as $reviewer) {
            if (!in_array($reviewer, $proposalEvaluations)) {
                $res[] = User::find($reviewer);
            }
        }
        resolve(ERIHSMailService::class)->weeklyReminder($res, Proposal::find($this->argument('proposalID')));
    }

}
