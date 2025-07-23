<?php

namespace App\Providers;

use App\Models\Method;
use App\Models\PostAccessReport;
use App\Models\Proposal;
use App\Models\ProposalEvaluation;
use App\Models\ProposalReviewer;
use App\Models\ProposalService;
use App\Models\Tool;
use App\Models\User;
use App\Observers\MethodObserver;
use App\Observers\PostAccessReportObserver;
use App\Observers\ProposalEvaluationObserver;
use App\Observers\ProposalObserver;
use App\Observers\ProposalReviewerObserver;
use App\Observers\ProposalServiceObserver;
use App\Observers\ToolObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // add your listeners (aka providers) here
            \SocialiteProviders\Orcid\OrcidExtendSocialite::class.'@handle',
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        User::class => [UserObserver::class],
        Proposal::class => [ProposalObserver::class],
        ProposalService::class => [ProposalServiceObserver::class],
        ProposalReviewer::class => [ProposalReviewerObserver::class],
        ProposalEvaluation::class => [ProposalEvaluationObserver::class],
        PostAccessReport::class => [PostAccessReportObserver::class],
        Method::class => [MethodObserver::class],
        Tool::class => [ToolObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
