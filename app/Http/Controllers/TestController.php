<?php

namespace App\Http\Controllers;

use App\Mail\NewSubmission;
use App\Models\Proposal;
use App\ProposalStatusActivities\SentMailStatusActivity;
use Illuminate\Support\Arr;

class TestController extends Controller
{
    public function index() {



        $proposal = Proposal::orderBy('id','DESC')->first();

        $leader = $proposal->leader;
        $applicants = $proposal->applicants;

        $proposalHistory = $proposal->status_history;

//        $proposalHistory = Arr::only($proposalHistory,[0,1,2,3,4]);
//        $proposal->status = 'under_review';
//        $proposal->status_history = $proposalHistory;
//
//        $proposalActivities = $proposal->activities;
//
//        $proposalActivities = Arr::where($proposalActivities,function ($item) {
//            return $item['statusKey'] <= 4;
//        });
//
//        $proposal->activities = $proposalActivities;
//
//        $proposal->save();


//        $services = $proposal->services;
//
//        $firstService = $services->first();
//        $instrumentScientist = $firstService->instrumentScientist;

        $applicationHistory = $proposal->getApplicationHistory('getViewData',[null,'application_history']);

        $statusActivities = $proposal->getLastStatusActivities();


        echo "<pre>";

        echo json_encode([
            "leader" => $leader,
            "applicants" => $applicants,
            "name" => $proposal->name,
            "status" => $proposal->getLastStatus(),
//            "organizations_users" => $proposal->getOrganizationsUsers(),
//            "instrument_scientists" => $instrumentScientist,
//            "services" => $services,
//            "application_history" => $applicationHistory,
            "status_activities" => $statusActivities,
//            "history_with_activities" => $proposal->getStatusHistoryWithActivities(),
        ]);
        echo "</pre>";

        return;


        $activities = [
            [
                [
                    'type' => 'email',
                    'timestamp' => "001",
                    "status" => 0,
                ],
                [
                    'type' => 'email2',
                    'timestamp' => "002",
                    "status" => 0,
                ],
            ],
            [
                [
                    'type' => 'email',
                    'timestamp' => "003",
                    "status" => 1,
                ],
            ],
            [
                [
                    'type' => 'email',
                    'timestamp' => "004",
                    "status" => 2,
                ],
                [
                    'type' => 'email2',
                    'timestamp' => "005",
                    "status" => 2,
                ],
            ]

        ];

        echo "<pre>";

        echo json_encode([
            'flatten' => Arr::flatten($activities),
            'normale' => $activities,
        ]);
        echo "</pre>";
    }

}
