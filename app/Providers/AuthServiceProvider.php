<?php

namespace App\Providers;

use App\Models\ConnectedAccount;
use App\Models\User;
use App\Policies\ConnectedAccountPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\MailTemplatePolicy;
use App\Policies\ResourceLockPolicy;
use App\Policies\RolePolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Tags\Tag;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ConnectedAccount::class => ConnectedAccountPolicy::class,
        Role::class       => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        MailTemplate::class => MailTemplatePolicy::class,
        User::class => UserPolicy::class,
        Tag::class => TagPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('view-user-roles', function ($user) {
            return $user->hasPermissionTo('administer users');
        });

        // For kenepa/resource-lock
        Gate::define('unlock', function (User $user) {
            return $user->hasPermissionTo('administer proposals');
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });
    }
}
