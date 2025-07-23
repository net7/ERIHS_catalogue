<?php

namespace App\Policies;

use Spatie\MailTemplates\Models\MailTemplate;
use App\Models\User;

class MailTemplatePolicy
{


    // this will trigger before any other check
    public function before(User $user): bool|null
    {
        if ($user->hasPermissionTo('administer site') || $user->hasRole(User::HELP_DESK_ROLE)){
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('administer site');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MailTemplate $mailTemplate): bool
    {

        return $this->viewAny($user);

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->viewAny($user);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MailTemplate $mailTemplate): bool
    {
        return $this->viewAny($user);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MailTemplate $mailTemplate): bool
    {
        return $this->viewAny($user);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MailTemplate $mailTemplate): bool
    {
        return $this->viewAny($user);

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MailTemplate $mailTemplate): bool
    {
        return $this->viewAny($user);

    }
}
