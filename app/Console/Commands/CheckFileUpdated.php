<?php

namespace App\Console\Commands;

use App\Enums\MolabAuthorizationDroneFlight;
use App\Enums\MolabOwnershipConsent;
use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Services\ERIHSMailService;
use DateTime;
use Illuminate\Console\Command;

class CheckFileUpdated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the user has uploaded the permissions files';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $proposals = Proposal::where('status', ProposalStatus::RANKED_MAIN_LIST)
            ->orWhere('status', ProposalStatus::ACCEPTED)
            ->get();
        foreach ($proposals as $proposal) {
            $history = $proposal->status_history;
            if (empty($history)) {
                continue;
            }
            $timeRankedInMainList = self::getTimeStatusFromHistory($history, ProposalStatus::RANKED_MAIN_LIST);
            $timeAcceptance = self::getTimeStatusFromHistory($history, ProposalStatus::ACCEPTED);
            $rankedDate = new DateTime($timeRankedInMainList);
            $today = new DateTime();
            if ($today->diff($rankedDate)->days == 15) {
                if (isset($timeRankedInMainList) && !isset($timeAcceptance)) {
                    (new ERIHSMailService())->acceptProposalReminder($proposal);
                }
                else if (isset($timeRankedInMainList) && isset($timeAcceptance)) {
                    $molabObjectsData = $proposal->molab_objects_data;
                    $sendReminder = false;
                    foreach ($molabObjectsData as $molabObject) {
                        if ($molabObject['molab_object_ownership_consent'] == MolabOwnershipConsent::REQUESTED->name ||
                            $molabObject['molab_object_ownership_consent'] == MolabOwnershipConsent::OTHER->name) {
                            $sendReminder = true;
                        }
                        if ($molabObject['molab_object_ownership_consent'] == MolabOwnershipConsent::RECEIVED->name &&
                            empty($molabObject['molab_object_ownership_consent_file'])) {
                            $sendReminder = true;
                        }
                    }
                    if ($proposal->molab_drone_flight == MolabAuthorizationDroneFlight::REQUESTED->name ||
                        $proposal->molab_drone_flight == MolabAuthorizationDroneFlight::OTHER->name) {
                        $sendReminder = true;
                    }
                    if ($proposal->molab_drone_flight == MolabAuthorizationDroneFlight::RECEIVED->name &&
                        empty($proposal->molab_drone_flight_file)) {
                        $sendReminder = true;
                    }
                    if ($proposal->molab_x_ray && !isset($proposal->molab_x_ray_file)) {
                        $sendReminder = true;
                    }

                    if ($sendReminder) {
                        (new ERIHSMailService())->updateFileReminder($proposal);
                    }
                }
            }
            if ($today->diff($rankedDate)->days >= 25) {
                (new ERIHSMailService())->acceptOrDiscardProposal($proposal);
            }
        }
    }


    public static function getTimeStatusFromHistory($history, $status)
    {
        foreach ($history as $historyItem) {
            if ($historyItem['status_code'] == $status->value) {
                return $historyItem['timestamp'];
            }
        }
        return null;
    }
}
