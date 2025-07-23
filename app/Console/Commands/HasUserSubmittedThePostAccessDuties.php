<?php

namespace App\Console\Commands;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Services\ERIHSMailService;
use DateTime;
use Illuminate\Console\Command;

class HasUserSubmittedThePostAccessDuties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:require-post-access-duties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent an email to the leader/alias to require the post access duties';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $proposals = Proposal::query()->where('status', ProposalStatus::ACCESS_CLOSED)->get();
        foreach ($proposals as $proposal) {
            $history = $proposal->getApplicationHistory();
            $access_closed_time = self::getAccessClosedTime($history);
            if($access_closed_time) {
                $access_closed_time = new DateTime($access_closed_time);
                $today = new DateTime();
                if($today->diff($access_closed_time)->days > 60) { //two months
                    $proposal->makeTransitionAndSave(ProposalStatus::MISSED_POST_ACCESS_DUTIES_DEADLINE);
                }
            }
        }
    }


    public static function getAccessClosedTime($history) {
        foreach ($history as $history_item) {
            if($history_item['status_code'] == ProposalStatus::ACCESS_CLOSED->value) {
                return $history_item['timestamp'];
            }
        }
        return null;
    }
}
