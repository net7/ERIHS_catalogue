<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getProposalStatistics($callId = null)
    {
        return [
            'total_submitted' => $this->getTotalSubmittedProposals($callId),
            'submitted_by_country' => $this->getSubmittedProposalsByCountry($callId),
            'total_accepted' => $this->getTotalAcceptedProposals($callId),
            'accepted_by_country' => $this->getAcceptedProposalsByCountry($callId),
            'acceptance_rate' => $this->getAcceptanceRate($callId),
            'proposals_by_country' => $this->getProposalsByCountry($callId),
            'proposals_by_gender' => $this->getProposalsByGender($callId),
            'proposals_by_type' => $this->getProposalsByType($callId),
            'proposals_by_status' => $this->getProposalsByStatus($callId),
            'proposals_by_discipline' => $this->getProposalsByDiscipline($callId),
            'accepted_by_discipline' => $this->getAcceptedProposalsByDiscipline($callId),
            'total_users' => $this->getTotalUsers($callId),
            'users_per_proposal' => $this->getUsersPerProposal($callId),
            'tools_requested' => $this->getToolsRequested($callId),
            'proposal_list_by_country' => $this->getProposalListByCountry($callId),
            'distribution_by_discipline' => $this->getDistributionByDiscipline($callId),
        ];
    }

    public function baseQuery($callId = null)
    {
        $query = Proposal::query();
        if ($callId) {
            $query->where('call_id', $callId);
        }
        return $query;
    }

    public function getTotalSubmittedProposals($callId = null)
    {
        return $this->baseQuery($callId)->count();
    }

    public function getSubmittedProposalsByCountry($callId = null)
    {
        return $this->baseQuery($callId)
        ->join('applicant_proposal', 'proposals.id', '=', 'applicant_proposal.proposal_id')
        ->join('users', 'applicant_proposal.applicant_id', '=', 'users.id')
        ->where('applicant_proposal.leader', 1)
        ->select('users.country', DB::raw('count(*) as total'))
        ->groupBy('users.country')
        ->get();
    }

    public function getTotalAcceptedProposals($callId = null)
    {
        return $this->baseQuery($callId)
            ->where('status', 'ranked_main_list')
            ->count();
    }

    public function getAcceptedProposalsByCountry($callId = null)
    {
        return $this->baseQuery($callId)
            ->join('applicant_proposal', 'proposals.id', '=', 'applicant_proposal.proposal_id')
            ->join('users', 'applicant_proposal.applicant_id', '=', 'users.id')
            ->where('applicant_proposal.leader', 1)
            ->where('status', 'ranked_main_list')
            ->select('users.country', DB::raw('count(*) as total'))
            ->groupBy('users.country')
            ->get();
    }

    public function getAcceptanceRate($callId = null)
    {
        $total = $this->getTotalSubmittedProposals($callId);
        if ($total === 0) return 0;

        return ($this->getTotalAcceptedProposals($callId) / $total) * 100;
    }

    public function getProposalsByCountry($callId = null)
    {
        return $this->baseQuery($callId)
            ->join('applicant_proposal', 'proposals.id', '=', 'applicant_proposal.proposal_id')
            ->join('users', 'applicant_proposal.applicant_id', '=', 'users.id')
            ->where('applicant_proposal.leader', 1)
            ->select('users.country', DB::raw('count(*) as total'))
            ->groupBy('users.country')
            ->orderBy('total', 'desc')
            ->get();
    }

    public function getProposalsByGender($callId = null)
    {
        return $this->baseQuery($callId)
            ->join('applicant_proposal', 'proposals.id', '=', 'applicant_proposal.proposal_id')
            ->join('users', 'applicant_proposal.applicant_id', '=', 'users.id')
            ->where('applicant_proposal.leader', 1)
            ->select('users.gender', DB::raw('count(*) as total'))
            ->groupBy('users.gender')
            ->get();
    }

    public function getProposalsByType($callId = null)
    {
        return $this->baseQuery($callId)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();
    }

    public function getProposalsByStatus($callId = null)
    {
        return $this->baseQuery($callId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
    }

    public function getProposalsByDiscipline($callId = null)
    {
        return $this->baseQuery($callId)
            ->join('proposal_service', 'proposals.id', '=', 'proposal_service.proposal_id')
            ->join('taggables', function ($join) {
                $join->on('proposal_service.service_id', '=', 'taggables.taggable_id')
                     ->where('taggables.taggable_type', Service::class);
            })
            ->join('tags', function ($join) {
                $join->on('taggables.tag_id', '=', 'tags.id')
                     ->where('tags.type', 'research_disciplines');
            })
            ->select('tags.name as research_disciplines', DB::raw('count(*) as total'))
            ->groupBy('tags.name')
            ->get();
    }

    public function getAcceptedProposalsByDiscipline($callId = null)
    {
        return $this->baseQuery($callId)
            ->where('status', 'accepted')
            ->join('proposal_service', 'proposals.id', '=', 'proposal_service.proposal_id')
            ->join('taggables', function ($join) {
                $join->on('proposal_service.service_id', '=', 'taggables.taggable_id')
                     ->where('taggables.taggable_type', Service::class);
            })
            ->join('tags', function ($join) {
                $join->on('taggables.tag_id', '=', 'tags.id')
                     ->where('tags.type', 'research_disciplines');
            })
            ->select('tags.name as research_disciplines', DB::raw('count(*) as total'))
            ->groupBy('tags.name')
            ->get();
    }

    public function getTotalUsers()
    {
        return User::count();
    }

    public function getUsersPerProposal($callId = null)
    {
        return $this->baseQuery($callId)
            ->withCount('applicants')
            ->get();
    }

    public function getToolsRequested($callId = null)
    {
        return $this->baseQuery($callId)
            ->join('proposal_tool', 'proposals.id', '=', 'proposal_tool.proposal_id')
            ->join('tools', 'proposal_tool.tool_id', '=', 'tools.id')
            ->select('tools.id', 'tools.name', DB::raw('count(*) as total'))
            ->groupBy('tools.id', 'tools.name')
            ->get();
    }

    public function getProposalListByCountry($callId = null)
    {
        return $this->baseQuery($callId)
            ->join('applicant_proposal', 'proposals.id', '=', 'applicant_proposal.proposal_id')
            ->join('users', 'applicant_proposal.applicant_id', '=', 'users.id')
            ->where('applicant_proposal.leader', 1)
            ->select('proposals.id', 'proposals.name', 'users.country', 'applicant_proposal.applicant_id as leader_id', 'proposals.created_at')
            ->orderBy('users.country')
            ->orderBy('proposals.created_at')
            ->get();
    }

    public function getDistributionByDiscipline($callId = null)
    {
        return $this->baseQuery($callId)
            ->join('proposal_service', 'proposals.id', '=', 'proposal_service.proposal_id')
            ->join('taggables', function ($join) {
                $join->on('proposal_service.service_id', '=', 'taggables.taggable_id')
                     ->where('taggables.taggable_type', Service::class);
            })
            ->join('tags', function ($join) {
                $join->on('taggables.tag_id', '=', 'tags.id')
                     ->where('tags.type', 'research_disciplines');
            })
            ->select('tags.name as scientific_domain', DB::raw('count(*) as total'))
            ->groupBy('tags.name')
            ->orderBy('total', 'desc')
            ->get();
    }
}

