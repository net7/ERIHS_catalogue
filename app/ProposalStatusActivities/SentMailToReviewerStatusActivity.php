<?php

namespace App\ProposalStatusActivities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SentMailToReviewerStatusActivity extends SentMailStatusActivity
{
    public static $type = 'sent_mail_to_reviewer';

}
