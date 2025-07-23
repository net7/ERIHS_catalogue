<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Services\ProposalService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProposalPolicy
{

    // this will trigger before any other check
    public function before(User $user): bool|null
    {
        if (
            $user->hasPermissionTo('administer site') ||
            $user->hasPermissionTo('administer proposals')
        ){
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['administer proposals','evaluate proposals']);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        $myProposals = ProposalService::mySubmittedProposal($user);
        if ($myProposals->contains($proposal)){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('administer proposals');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        $myProposals = ProposalService::mySubmittedProposal($user);
        if ($myProposals->contains($proposal)){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Proposal $proposal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Proposal $proposal): bool
    {
        return false;
    }
}
