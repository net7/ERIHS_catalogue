<?php

namespace App\ProposalStatusActivities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SentMailToOrganizationStatusActivity extends SentMailStatusActivity
{

    public static $type = 'sent_mail_to_organization';

    protected $organizationId;
    protected $serviceId;
    public function __construct($serviceId = [], $organizationId = [], $usersIds = [], $emailType = null, $data = [], $timestamp = null, $model = null, $statusKey = null)
    {
        $this->serviceId = $serviceId;
        $this->organizationId = $organizationId;

        $data = array_merge($data,[
            'serviceId' => $serviceId,
            'organizationId' => $organizationId,
        ]);
        parent::__construct($usersIds,$emailType,$data,$timestamp,$model,$statusKey);

    }
}
