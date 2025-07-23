<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ERIHSMailService;
use App\Services\UserService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserObserver
{

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->assignRole(USER::USER_ROLE);
        try {
            resolve(ERIHSMailService::class)->notifyNewUser($user);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {

        //TODO: does this get called if you change the role of a user ?

        User::withoutEvents(function () use ($user) {
            if ((new UserService())->hasAllMandatoryFieldsFilled($user)){
                $user->complete_profile = true;
                $user->save();
            } else {
                if ($user->complete_profile){
                    $user->complete_profile = false;
                    $user->save();
                }
            }
        });
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
