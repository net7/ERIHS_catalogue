<?php

namespace App\Services;

use App\Models\PostAccessReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PostAccessReportService
{
    public static function getMyPostAccessReportsQuery($user = null, $proposal_id = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        $q = PostAccessReport::query();

        if ($proposal_id) {
            $q->where('proposal_id', '=', $proposal_id);
        }
        if ($user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE])) {
            return $q;
        }

        $q->where('user_id', '=', $user->id);
        return $q;
    }
}
