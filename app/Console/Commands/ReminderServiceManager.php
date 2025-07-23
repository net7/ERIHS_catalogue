<?php

namespace App\Console\Commands;

use App\Models\Proposal;
use App\Models\ProposalService;
use App\Models\Service;
use App\Models\User;
use App\Services\ERIHSMailService;
use DateTime;
use Illuminate\Console\Command;

class ReminderServiceManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:solicit-service-manager-to-close-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'An email will be sent to the service manager to close access';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $proposalService = ProposalService::where('access', ProposalService::ACCESS_SCHEDULED)->get();
        foreach ($proposalService as $item) {
            $proposal = Proposal::find($item->proposal_id);
            $service = Service::find($item->service_id);
            $today = new DateTime();
            $scheduleDate = new DateTime($item->scheduled_date);
            if ($today->diff($scheduleDate)->days % 7 == 0) {
                (new ERIHSMailService())->solicitServiceManagersToCloseAccess($proposal, $service->serviceManagers);
            }
        }
    }
}
