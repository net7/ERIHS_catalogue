<?php

namespace App\Services;

use App\Enums\ProposalReviewerStatus;
use App\Enums\ProposalStatus;
use App\Enums\ProposalStatusGroups;
use App\Livewire\CreateProposal;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserService
{

    public function hasAllMandatoryFieldsFilled(User $user): bool
    {

        foreach (User::$mandatoryFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        foreach ($user->roles() as $role) {
            if (isset(User::$additionalMandatoryFieldsByRole[$role])) {
                foreach (User::$additionalMandatoryFieldsByRole[$role] as $role) {
                    if (empty($user->$field)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public static function isUserReviewerOfProposal(?User $user, Proposal $proposal): bool
    {
        $proposal_reviewer = ProposalReviewer::query()
            ->where('proposal_id', '=', $proposal->id)
            ->where('reviewer_id', '=', $user->id)
            ->first();
        if ($proposal_reviewer) {
            return true;
        }
        return false;
    }

    public static function canUserEvaluateProposal(?User $user, Proposal $proposal, bool $ignoreAcceptance = false): bool
    {
        if (!$user->can('evaluate proposals')) {
            return false;
        }

        $proposalReviewer = ProposalReviewer::query()
            ->where('proposal_id', '=', $proposal->id)
            ->where('reviewer_id', '=', $user->id)
            ->first();

        if (!$proposalReviewer || $proposalReviewer->status == ProposalReviewerStatus::SKIPPED->name) {
            return false;
        }

        if ($ignoreAcceptance && UserService::isUserReviewerOfProposal($user, $proposal)) {
            return true;
        }

        // if the user is a reviewer and has accepted to do the review, or if the user is a reviewer and the acceptance is ignored, then the user can evaluate the proposal
        if (
            UserService::isUserReviewerOfProposal($user, $proposal) &&
            UserService::acceptedToDoTheReview($user, $proposal)
        ) {
            return true;
        }
        return false;
    }

    public static function acceptedToDoTheReview(?User $user, Proposal $proposal): bool
    {
        $proposal_reviewer = ProposalReviewer::query()
            ->where('proposal_id', '=', $proposal->id)
            ->where('reviewer_id', '=', $user->id)
            ->first();

        if ($proposal_reviewer && $proposal_reviewer->status == ProposalReviewerStatus::ACCEPTED->name) {
            return true;
        }

        return false;
    }

    public static function canUserCloseProposalEvaluations(User $user): bool
    {
        if (!$user->can('close evaluations')) {
            return false;
        }
        return true;
    }

    /**
     * Devo verificare che lo user passato sia
     * un service manager di uno dei servizi della proposal,
     *  e che la proposal stessa non sia già passata in uno stato successivo a quello di submitted
     * @param \App\Models\User $user
     * @return bool
     */
    public static function canUserEvaluateFeasability(User $user, Proposal $proposal): bool
    {
        if (env('IS_TEST', false) && $user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        if ($proposal->status !== ProposalStatus::SUBMITTED->value && $proposal->status !== ProposalStatus::RESUBMITTED->value) {
            return false;
        }



        $userId = $user->getKey();

        $hasServiceWithSameOrganization = $proposal->servicesByServiceManager($userId)->exists();

        if ($hasServiceWithSameOrganization) {
            return true;
        }
        return false;
    }

    public static function canUserEditProposal(?User $user, ?Proposal $proposal): bool
    {
        if (env('IS_TEST', false) && $user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        if (
            $user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]) &&
            $proposal->isCurrentlyInGroup(ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value)
        ) {
            return true;
        }

        if ($user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        return false;
    }

    public static function canUpdateFiles(?User $user, ?Proposal $proposal): bool
    {
        if ($proposal->isCurrentlyInGroup(ProposalStatusGroups::FINAL->value)) {
            return false;
        }

        if (
            $user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]) ||
            $proposal->isUserLeader($user) ||
            $proposal->isUserAlias($user)
        ) {
            $services = $proposal->services()->get();
            $platforms  = CreateProposal::getPlatforms($services);
            if ($platforms->contains('Molab')) {
                return true;
            }
        }


        return false;
    }

    public static function userHasObjectType($user, $types): bool
    {
        $userTypes = $user->object_types;
        foreach ($userTypes as $type) {
            if (in_array($type, $types)) {
                return true;
            }
        }

        return false;
    }


    public static function createJsonToSend($user)
    {
        $item = [];
        $item['id'] = $user->id;
        $item['first_name'] = $user->name;
        $item['last_name'] = $user->surname;
        $item['full_name'] = $user->full_name;
        $item['mbox'] = $user->email;
        $item['img_url'] = $user->profile_photo_path ?? '';
        $item['phone'] = $user->office_phone ?? '';

        $tags = $user->tags()->get();
        foreach ($tags as $tag) {
            $item[$tag->type][] = $tag->external_id ?? $tag->name;
        }
        return $item;
    }

    public static function canUserScheduleAccess(User $user, Proposal $proposal): bool
    {
        if (env('IS_TEST', false) && $user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        if (
            $proposal->status !== ProposalStatus::FILES_CONFIRMED->value &&
            $proposal->status != ProposalStatus::ACCESS_SCHEDULED->value
        ) {
            return false;
        }

        $userId = $user->getKey();

        $hasServiceWithSameOrganization = $proposal->servicesByServiceManager($userId)->exists();

        if ($hasServiceWithSameOrganization) {
            return true;
        }
        return false;
    }

    public static function canEditPostAccessReport(User $user, Proposal $proposal): bool
    {
        if (env('IS_TEST', false) && $user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        if ($proposal->status !== ProposalStatus::ACCESS_CLOSED->value) {
            return false;
        }
        if ($proposal->postDutiesReport && ($proposal->isUserLeaderOrAlias($user->id) || $user->can('administer proposals'))) {
            return true;
        }

        return false;
    }

    public static function getProposalsIdsForFeasibility(User $user = null)
    {
        if (is_null($user)) {
            $user = Auth::user();
        }
        if (!$user->hasRole('SERVICE_MANAGER')) {
            return [-1];
        }
    }
}
