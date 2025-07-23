<?php

namespace App\ProposalStatusActivities;

use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EvaluationStatusActivity extends StatusActivity
{

    public static $type = 'evaluation';


    protected $proposalEvaluationData;

    /**
     * @param array $data
     */
    public function __construct($proposalEvaluationData = [], $data = [], $timestamp = null, $model = null, $statusKey = null)
    {
        unset($proposalEvaluationData['proposal']);
        $this->proposalEvaluationData = $proposalEvaluationData;

        $data = array_merge($data, [
            'proposalEvaluationData' => $proposalEvaluationData,
        ]);
        parent::__construct($data, $timestamp, $model, $statusKey);

    }

    public function buildViewDataApplicationHistory(User $user = null)
    {

        $proposalEvaluationData = $this->proposalEvaluationData;

        $reviewerId = Arr::get($proposalEvaluationData, 'reviewer_id');

        if ($reviewerId) {
            $reviewer = User::find($reviewerId);
        }


        if (!$reviewer) {
            $reviewerString = "N.D.";
        } else {
            $reviewerString = $reviewer->surname . ' ' . $reviewer->name;
        }


        $sections = [
            'excellence_relevance',
            'excellence_methodology',
            'excellence_originality',
            'excellence_expertise',
            'excellence_timeliness',
            'excellence_state_of_the_art',
            'impact_research',
            'impact_knowledge_sharing',
            'impact_innovation_potential',
            'impact_open_access',
            'impact_expected_impacts',
        ];


        $viewString = "<span class='font-bold'>$reviewerString</span> has evaluated the proposal: <br/>";

        foreach ($sections as $section) {
            $viewString .= Str::title(Str::replace('_',' ',$section)) . ": ";
            $viewString .= number_format(Arr::get($proposalEvaluationData,$section,0),2,'.','');
            $viewString .= '/5';
            $viewString .= '<br/>';
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
