<?php

namespace App\ProposalStatusActivities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SentMailStatusActivity extends StatusActivity
{

    public static $type = 'sent_mail';


    protected $usersIds = [];

    protected $emailType;

    /**
     * @param array $data
     */
    public function __construct($usersIds = [], $emailType = null, $data = [], $timestamp = null, $model = null, $statusKey = null)
    {
        $this->usersIds = $usersIds;
        $this->emailType = $emailType;

        $data = array_merge($data,[
            'usersIds' => $usersIds,
            'emailType' => $emailType
        ]);
        parent::__construct($data,$timestamp,$model,$statusKey);

    }

    public function getUsers() {
        if (!$this->usersIds) {
            return collect();
        }
        return User::whereIn('id',array_keys($this->usersIds))
            ->get();
    }
    public function buildViewDataApplicationHistory(User $user = null) {

        $users = $this->getUsers();

        if (!$user || !$user->hasAnyRole(['help desk','admin'])) {
            return "An e-mail has been sent about the application";
        }

        if (count($users) <= 0) {
            return "No e-mail addresses have been found";
        }

        return "An e-mail has been sent to the following recipients: "
            . implode("; ",$users->pluck('email')->all());

    }


}
