<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalEvaluation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public static function calculateAverages($proposalId)
    {
        $evaluations = self::where('proposal_id', $proposalId)->get();
        $totalEvaluations = $evaluations->count();
        if ($totalEvaluations > 0) {
            $totalWeightedScore = 0;
            foreach ($evaluations as $evaluation) {
                $totalWeightedScore += self::calculateSingleWeightedAverage(
                    $evaluation->excellence_relevance,
                    $evaluation->excellence_methodology,
                    $evaluation->excellence_originality,
                    $evaluation->excellence_expertise,
                    $evaluation->excellence_timeliness,
                    $evaluation->excellence_state_of_the_art,
                    $evaluation->impact_research,
                    $evaluation->impact_knowledge_sharing,
                    $evaluation->impact_innovation_potential,
                    $evaluation->impact_open_access,
                    $evaluation->impact_expected_impacts
                );
            }

            return number_format($totalWeightedScore / $totalEvaluations, 2);
        }
        return 0;
    }

    public static function calculateSingleWeightedAverage(
        $excellence_relevance,
        $excellence_methodology,
        $excellence_originality,
        $excellence_expertise,
        $excellence_timeliness,
        $excellence_state_of_the_art,
        $impact_research,
        $impact_knowledge_sharing,
        $impact_innovation_potential,
        $impact_open_access,
        $impact_expected_impacts    


    ): float {

        return
            ($excellence_relevance * config('app.proposal_evaluation_weight.excellence_relevance')) +
            ($excellence_methodology * config('app.proposal_evaluation_weight.excellence_methodology')) +
            ($excellence_originality * config('app.proposal_evaluation_weight.excellence_originality')) +
            ($excellence_expertise * config('app.proposal_evaluation_weight.excellence_expertise')) +
            ($excellence_timeliness * config('app.proposal_evaluation_weight.excellence_timeliness')) +
            ($excellence_state_of_the_art * config('app.proposal_evaluation_weight.excellence_state_of_the_art')) +
            ($impact_research * config('app.proposal_evaluation_weight.impact_research')) +
            ($impact_knowledge_sharing * config('app.proposal_evaluation_weight.impact_knowledge_sharing')) +
            ($impact_innovation_potential * config('app.proposal_evaluation_weight.impact_innovation_potential')) +
            ($impact_open_access * config('app.proposal_evaluation_weight.impact_open_access')) +
            ($impact_expected_impacts * config('app.proposal_evaluation_weight.impact_expected_impacts'));
    }
}
