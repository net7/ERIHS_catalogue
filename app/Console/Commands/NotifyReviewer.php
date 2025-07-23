<?php

namespace App\Console\Commands;

use App\Enums\ProposalReviewerRefusalReason;
use App\Enums\ProposalReviewerStatus;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use App\Services\ERIHSMailService;
use DateTime;
use Illuminate\Console\Command;

class NotifyReviewer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-reviewer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If the reviewer doesn\'t check by 7/10 days, an email will be sent to the reviewer and the UH about the situation ';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $proposalReviewers = ProposalReviewer::query()
            ->where('status', '=', ProposalReviewerStatus::WAITING)
            ->get();
        $today = new DateTime();
        foreach ($proposalReviewers as $item) {
            $reviewer = User::find($item->reviewer_id);
            if ($reviewer) {
                $assignment_date = new DateTime($item->confirmed_at); //date when the reviewer was assigned
                $proposal = Proposal::find($item->proposal_id);
                if ($today->diff($assignment_date)->days == 7) {
                    resolve(ERIHSMailService::class)->reviewerMissedCheck($reviewer, $proposal);
                }

                if ($today->diff($assignment_date)->days >= 10) { //have the reviewer checked the conflict of interest in the next 3 days?
                    resolve(ERIHSMailService::class)->newReviewerSelection($reviewer, $proposal);
                    $item->update([
                        'status' => ProposalReviewerStatus::REFUSED,
                        'refused_reason' => ProposalReviewerRefusalReason::TIMED_OUT->name,
                        'refused_at' => now(),
                    ]);
                }
            }
        }
    }
}
