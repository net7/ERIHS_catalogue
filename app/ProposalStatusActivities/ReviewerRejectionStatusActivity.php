<?php

namespace App\ProposalStatusActivities;

use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewerRejectionStatusActivity extends StatusActivity
{

    public static $type = 'reviewer_rejection';


    protected $proposalReviewerData;

    /**
     * @param array $data
     */
    public function __construct($proposalReviewerData = [], $data = [], $timestamp = null, $model = null, $statusKey = null)
    {
        unset($proposalReviewerData['proposal']);
        unset($proposalReviewerData['reviewer']);
        $this->proposalReviewerData = $proposalReviewerData;

        $data = array_merge($data, [
            'proposalReviewerData' => $proposalReviewerData,
        ]);
        parent::__construct($data, $timestamp, $model, $statusKey);

    }

    public function buildViewDataApplicationHistory(User $user = null)
    {

        $proposalReviewerData = $this->proposalReviewerData;

        $reviewerId = Arr::get($proposalReviewerData, 'reviewer_id');

        if ($reviewerId) {
            $reviewer = User::find($reviewerId);
        }


        if (!$reviewer) {
            $reviewerString = "N.D.";
        } else {
            $reviewerString = $reviewer->surname . ' ' . $reviewer->name;
        }


        $viewString = "<span class='font-bold'>$reviewerString</span> has rejected to review the proposal.";
        $viewString .= "<br/><span class='font-bold'>Motivation</span>: " . Arr::get($proposalReviewerData,"refused_reason");
        $comment = Arr::get($proposalReviewerData,"refused_comment");
        if ($comment) {
            $viewString .= "<br/><span class='font-bold'>Further comment</span>: " . $comment;
        }
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
