<?php

namespace App\ProposalStatusActivities;

use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AccessCarriedOutStatusActivity extends StatusActivity
{

    public static $type = 'access_carried_out';


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

        if (!$service) {
            $service = new Service();
        }

        $serviceString = $service->title;

        $viewString = "The access for the service " .
            "<span class='italic'>$serviceString</span> has been carried out.<br/> ";


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
