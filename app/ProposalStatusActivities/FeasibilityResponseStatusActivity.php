<?php

namespace App\ProposalStatusActivities;

use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FeasibilityResponseStatusActivity extends StatusActivity
{

    public static $type = 'feasibility_response';


    protected $proposalServiceData;

    /**
     * @param array $data
     */
    public function __construct($proposalServiceData = [], $data = [], $timestamp = null, $model = null, $statusKey = null)
    {
        unset($proposalServiceData['proposal']);
        $this->proposalServiceData = $proposalServiceData;

        $data = array_merge($data, [
            'proposalServiceData' => $proposalServiceData,
        ]);
        parent::__construct($data, $timestamp, $model, $statusKey);

    }

    public function buildViewDataApplicationHistory(User $user = null)
    {

        $proposalServiceData = $this->proposalServiceData;

        $serviceId = Arr::get($proposalServiceData, 'service_id');

        if ($serviceId) {
            $service = Service::find($serviceId);
        }

        $serviceString = null;
        if (!$service) {
            $service = new Service();
        }

        $serviceString = $service->title;
        // $firstChoiceDateStart = $this->getDate(Arr::get($proposalServiceData, 'first_choice_start_date'));
        // $firstChoiceDateEnd = $this->getDate(Arr::get($proposalServiceData, 'first_choice_end_date'));
        // $secondChoiceDateStart = $this->getDate(Arr::get($proposalServiceData, 'second_choice_start_date'));
        // $secondChoiceDateEnd = $this->getDate(Arr::get($proposalServiceData, 'second_choice_start_date'));

        $result = Arr::get($proposalServiceData, 'feasible');
        $result = ($result == 'feasible') ? "FEASIBLE" : "NOT FEASIBLE";

        $viewString = "<span class='font-bold'>$result</span> - Feasibility response about the service " .
            "<span class='italic'>$serviceString</span>.<br/> ";
        $comment = Arr::get($proposalServiceData, 'motivation');
        if ($comment) {
            $viewString .= "Motivation: <span class='font-bold'>$comment</span><br/>";

        }
        // $viewString .= "First choice dates: from $firstChoiceDateStart to $firstChoiceDateEnd <br/>" .
        //     "Second choice dates: from $secondChoiceDateStart to $secondChoiceDateEnd <br/>" .
        //     "Number of days: " . Arr::get($proposalServiceData, 'number_of_days') . " <br/>";
        $viewString .=
        "Number of days: " . Arr::get($proposalServiceData, 'number_of_days') . " <br/>" .
        "Notes: " .  Arr::get($proposalServiceData, 'notes');

        return $viewString;

    }


    protected function getDate($date)
    {
        try {
            $date =
                Carbon::parse($date)
                    ->toDateString();
        } catch (\Throwable $e) {
            $date = null;
        }

        return $date;
    }
}
