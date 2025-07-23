<?php

namespace App\Services;

use App\Mail\AccessDuties;
use App\Mail\CloseAccess;
use App\Mail\NewUserRegistered;
use App\Mail\NoReviewersAvailable;
use App\Mail\ReviewerCheck;
use App\Mail\ReviewerConfirmation;
use App\Mail\ReviewerDeleted;
use App\Mail\ReviewerSelected;
use App\Mail\WeeklyReminder;
use App\Models\Proposal;
use App\Models\ProposalService;
use App\Models\User;
use App\ProposalStatusActivities\SentMailStatusActivity;
use App\ProposalStatusActivities\SentMailToApplicantUserStatusActivity;
use App\ProposalStatusActivities\SentMailToHelpDeskStatusActivity;
use App\ProposalStatusActivities\SentMailToOrganizationStatusActivity;
use App\ProposalStatusActivities\SentMailToReviewerStatusActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ERIHSMailService
{
    //when a new user is registered,
    //an email is sent to all users with a HELP DESK role
    public function notifyNewUser($newUser): void
    {
        $helpDeskUsers = User::with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', User::HELP_DESK_ROLE)->toArray()
        );
        foreach ($helpDeskUsers as $user) {
            Mail::to($user->email)->send(new NewUserRegistered($newUser));
        }
    }

    //an email is sent to the user, the help desk user and the organization to inform
    //of the submission
    public function applicationSubmitted(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'application_submitted');
        $this->sendMailToHelpdeskUsers($proposal, 'new_submission', $user);
        $this->sendMailToServiceManagers($proposal, 'check_feasibility');
        $this->sendMailToProposalPartners($proposal, 'partner_proposal');
    }

    public function applicationResubmitted(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'application_resubmission');
        $this->sendMailToHelpdeskUsers($proposal, 'application_resubmission_hd_notification', $user);
        $this->sendMailToServiceManagers($proposal, 'check_feasibility');
    }

    public function applicationAccepted(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'acceptance_confirmation');
        $this->sendMailToHelpdeskUsers($proposal, 'application_accepted', $user);
    }

    //An email will be sent to inform the UH about the feasibility
    public function applicationFeasible(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'application_feasible');
        $activity = new SentMailToApplicantUserStatusActivity(
            [$user->getKey() => $user->email],
            'help_desk_application_feasible'
        );
        $proposal->addActivityAndSave($activity);

        $this->sendMailToHelpdeskUsers($proposal, 'help_desk_application_feasible');
    }

    public function applicationPartiallyFeasible(User $user, Proposal $proposal): void
    {

        $this->sendMailToLeaderAndAlias($proposal, 'resubmit_proposal');
        $activity = new SentMailToApplicantUserStatusActivity(
            [$user->getKey() => $user->email],
            'application_partially_feasible'
        );
        $proposal->addActivityAndSave($activity);

        $this->sendMailToHelpdeskUsers($proposal, 'application_partially_feasible');
    }

    public function applicationNotFeasible(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'resubmit_proposal');
        $activity = new SentMailToApplicantUserStatusActivity(
            [$user->getKey() => $user->email],
            'application_partially_feasible'
        );
        $proposal->addActivityAndSave($activity);

        $this->sendMailToHelpdeskUsers($proposal, 'application_not_feasible');
    }

    public function confirmReviewer($proposal): void
    {
        $helpDeskUsers = User::with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', User::HELP_DESK_ROLE)->toArray()
        );
        foreach ($helpDeskUsers as $user) {
            Mail::to($user->email)->send(new ReviewerConfirmation($proposal));
        }
    }

    public function noReviewersAvailable($proposal): void
    {
        $helpDeskUsers = User::with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', User::HELP_DESK_ROLE)->toArray()
        );
        foreach ($helpDeskUsers as $user) {
            Mail::to($user->email)->send(new NoReviewersAvailable($proposal));
        }
    }

    //An email will be sent to the reviewers that they have been selected for the application
    public function selectReviewer(User $user, Proposal $proposal): void
    {
        Mail::to($user->email)->send(new ReviewerSelected($proposal));
        $activity = new SentMailToReviewerStatusActivity(
            [$user->getKey() => $user->email],
            'reviewer_selected'
        );
        $proposal->addActivityAndSave($activity);
    }

    public function reviewerDeleted(User $user, Proposal $proposal): void
    {
        Mail::to($user->email)->send(new ReviewerDeleted($proposal));
        $activity = new SentMailToReviewerStatusActivity(
            [$user->getKey() => $user->email],
            'reviewer_deleted'
        );
        $proposal->addActivityAndSave($activity);
    }

    //If the reviewer doesn't check by 7 days, an email will be sent to the reviewer
    //and the UH about the situation
    public function reviewerMissedCheck(User $reviewer, Proposal $proposal): void
    {
        //mail to reviewer
        Mail::to($reviewer->email)->send(new ReviewerCheck($proposal));

        $this->sendMailToHelpdeskUsers($proposal, 'help_desk_notification_after_seven_days');

        $activity = new SentMailToReviewerStatusActivity(
            [$reviewer->getKey() => $reviewer->email],
            'help_desk_notification_after_seven_days'
        );
        $proposal->addActivityAndSave($activity);
    }

    //If the reviewer doesn't check by 10 days, an email will be sent to the reviewer
    //and the UH about the situation
    public function newReviewerSelection(User $reviewer, Proposal $proposal): void
    {
        Mail::to($reviewer->email)->send(new ReviewerDeleted($proposal));
        $this->sendMailToHelpdeskUsers($proposal, 'new_reviewer_selection', $reviewer);
        $activity = new SentMailToReviewerStatusActivity(
            [$reviewer->getKey() => $reviewer->email],
            'new_reviewer_selection'
        );
        $proposal->addActivityAndSave($activity);
    }

    //An email informs the UH about the conflict and proposes another reviewer
    public function reviewerConflicts(User $user, Proposal $proposal): void
    {
        $this->sendMailToHelpdeskUsers($proposal, 'reviewer_conflicts', $user);
    }

    public function reviewerAcceptance(User $user, Proposal $proposal): void
    {
        $this->sendMailToHelpdeskUsers($proposal, 'reviewer_acceptance', $user);
    }

    public function reviewerExplicitRefusal(User $user, Proposal $proposal): void
    {
        $this->sendMailToHelpdeskUsers($proposal, 'reviewer_explicit_refusal', $user);
    }

    //For 1 month, an email will be sent weekly to remind
    //the reviewers to evaluate the proposal and click on the button "evaluation made".
    public function weeklyReminder($reviewers, Proposal $proposal): void
    {
        foreach ($reviewers as $reviewer) {
            Mail::to($reviewer->email)->send(new WeeklyReminder($proposal));
        }
    }

    //When two of the three reviewers has clicked on
    //the button "evaluation made", an email will be sent to the UH to inform about the evaluation
    public function proposalEvaluationUpdate(Proposal $proposal): void
    {
        $this->sendMailToHelpdeskUsers($proposal, 'proposal_evaluation_update');
    }

    public function thirdReviewerEvaluatedTheProposal(Proposal $proposal): void
    {
        $this->sendMailToHelpdeskUsers($proposal, 'third_reviewer_evaluated_the_proposal');
    }

    //An email will be sent to the user to inform of the decision and invite contacting the user helpdesk
    public function applicationReviewOutcome(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'application_review_outcome');
        $activity = new SentMailStatusActivity(
            $this->getLeaderAndAlias($proposal),
            'application_rejected'
        );
        $proposal->addActivityAndSave($activity);
    }

    //An email informs the organization/s that the application doesn't pass the review
    public function applicationRejected(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'application_rejected');

        $activity = new SentMailToApplicantUserStatusActivity(
            $this->getLeaderAndAlias($proposal),
            'application_rejected'
        );
        $proposal->addActivityAndSave($activity);

        $this->sendMailToServiceManagers($proposal, 'application_below_threshold');
    }

    //An email will be sent to the user with the access offer and to the organization to inform of the decision.
    //The user must confirm to accept the offer and contact the organization to carry out the access by 10 days
    public function applicationInMainList(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'application_in_main_list');
        $activity = new SentMailToApplicantUserStatusActivity(
            $this->getLeaderAndAlias($proposal),
            'application_in_main_list'
        );
        $proposal->addActivityAndSave($activity);

        $this->sendMailToServiceManagers($proposal, 'application_in_main_list_sm_notification');
    }

    public function applicationInReserveList(User $user, Proposal $proposal): void
    {
        $this->sendMailToLeaderAndAlias($proposal, 'application_in_reserve_list');
        $this->sendMailToServiceManagers($proposal, 'application_in_reserve_list_sm_notification');
        $activity = new SentMailToApplicantUserStatusActivity(
            $this->getLeaderAndAlias($proposal),
            'application_in_reserve_list'
        );
        $proposal->addActivityAndSave($activity);
    }

    //An email will be sent to the user, the organization and the UH to inform that the access has been carried out.
    //The status of the proposal is changed in "close" and asks the user for post access duties
    public function closeProposal(Proposal $proposal): void
    {
        $this->sendMailToHelpdeskUsers($proposal, 'close_proposal_hd_notification');
        $this->sendMailToServiceManagers($proposal, 'close_proposal_sm_notification');
        $this->sendMailToLeaderAndAlias($proposal, 'close_proposal');

        $activity = new SentMailToApplicantUserStatusActivity(
            $this->getLeaderAndAlias($proposal),
            'close_proposal'
        );
        $proposal->addActivityAndSave($activity);
    }

    //An automatic email will be sent to the user, the
    //organization and the UH to inform that the process is successfully closed.
    public function closeProcess(Proposal $proposal): void
    {
        $this->sendMailToHelpdeskUsers($proposal, 'close_process_hd_notification');
        $this->sendMailToLeaderAndAlias($proposal, 'close_process');
        $this->sendMailToServiceManagers($proposal, 'close_process_sm_notification');
    }

    //An email will be sent periodically (for 2 months)
    //to the user to require the post access duties
    public function accessDuties($user, Proposal $proposal): void
    {
        Mail::to($user->email)->send(new AccessDuties($proposal));
    }

    public function acceptProposalReminder($proposal)
    {
        $this->sendMailToHelpdeskUsers($proposal, 'user_not_yet_accepted_access_offer');
        $this->sendMailToLeaderAndAlias($proposal, 'accept_access_offer_reminder');
    }

    public function updateFileReminder($proposal)
    {
        $this->sendMailToHelpdeskUsers($proposal, 'user_not_yet_updated_files');
        $this->sendMailToLeaderAndAlias($proposal, 'update_files_reminder');
    }

    public function acceptOrDiscardProposal($proposal)
    {
        $this->sendMailToHelpdeskUsers($proposal, 'accept_or_discard_proposal');
    }

    public function proposalDiscarded($proposal)
    {
        $this->sendMailToHelpdeskUsers($proposal, 'proposal_discarded_from_hd', type: 'ProposalResource');
        $this->sendMailToLeaderAndAlias($proposal, 'proposal_discarded_from_hd', type: 'MyProposalResource');
        $this->sendMailToServiceManagers($proposal, 'proposal_discarded_from_hd', type: 'MyProposalResource');
        $activity = new SentMailStatusActivity(
            $this->getLeaderAndAlias($proposal),
            'proposal_discarded_from_hd'
        );
        $proposal->addActivityAndSave($activity);
    }

    public function proposalConfirmed($proposal)
    {
        $this->sendMailToServiceManagers($proposal, 'proposal_confirmed');
    }

    public function solicitServiceManagersToCloseAccess($proposal, Collection $serviceManagers)
    {
        foreach ($serviceManagers as $serviceManager){
            Mail::to($serviceManager->email)->send(new CloseAccess($proposal));
        }
    }

    protected function sendMailToHelpdeskUsers($proposal, $emailType, $user = null, $type = null)
    {

        $helpDeskUsers = User::role(User::HELP_DESK_ROLE)->get();
        $mailClassname = "App\\Mail\\" . Str::studly($emailType);

        foreach ($helpDeskUsers as $helpDeskUser) {
            switch ($emailType) {
                case 'new_submission':
                    $mailable = new $mailClassname($user, $proposal);
                    break;
                case 'reviewer_conflicts':
                case 'reviewer_acceptance':
                case 'reviewer_explicit_refusal':
                case 'application_resubmission_hd_notification':
                    $mailable = new $mailClassname($proposal, $user);
                    break;
                case 'help_desk_notification_after_seven_days':
                case 'new_reviewer_selection':
                    $mailable = new $mailClassname(reviewer: $user, proposal: $proposal);
                    break;
                case 'proposal_discarded_from_hd':
                    $mailable = new $mailClassname($proposal, $type);
                    break;
                default:
                    try {
                        $mailable = new $mailClassname($proposal);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                        $mailable = null;
                    }
                    break;
            }
            if ($mailable) {
                Mail::to($helpDeskUser->email)->send($mailable);
            }
        }

        $activity = new SentMailToHelpDeskStatusActivity(
            $helpDeskUsers->pluck('email', 'id')->all(),
            $emailType
        );
        $proposal->addActivityAndSave($activity);
    }

    protected function sendMailToServiceManagers($proposal, $emailType, $type = null)
    {

        //service managers
        $proposalServices = $proposal->proposalServices;

        $mailClassname = "App\\Mail\\" . Str::studly($emailType);
        $serviceManagerEmailSent = [];
        $proposalLeader = $proposal->getLeader();
        foreach ($proposalServices as $proposalService) {

            if ($emailType == 'check_feasibility' &&
                $proposalService->feasible != null &&
                $proposalService->feasible != ProposalService::TO_BE_DEFINED) {
                // we are after a second draft resubmission, we've re-set all the non feasible services,
                // we only send emails to those services' service managers
                continue;
            }

            $service = $proposalService->service;
            $organization = $service->organization;
            $organizationEmail = filter_var($organization->mbox, FILTER_VALIDATE_EMAIL);
            $usersIds = [];

            foreach ($service->serviceManagers as $serviceManager) {
                $userEmail = $serviceManager->email;
                if (in_array($userEmail, $serviceManagerEmailSent)) {
                    continue;
                }
                $serviceManagerEmailSent []= $userEmail;
                if ($proposalLeader && $userEmail) {
                    if (isset($type)) {
                        Mail::to($userEmail)->send(new $mailClassname($proposal, $type));
                    } else {
                        Mail::to($userEmail)->send(new $mailClassname($proposal, $proposalLeader));
                    }
                }
                $usersIds[$serviceManager->getKey()] = $userEmail;
            }

            $activity = new SentMailToOrganizationStatusActivity(
                [$service->getKey() => $service->title],
                [$organization->getKey() => $organizationEmail],
                $usersIds,
                $emailType
            );
            $proposal->addActivityAndSave($activity);
        }
    }

    protected function sendMailToLeaderAndAlias($proposal, $emailType, $type = null)
    {
        $mailClassname = "App\\Mail\\" . Str::studly($emailType);
        if (isset($type)) {
            Mail::to($proposal->getLeader()->email)->send(new $mailClassname($proposal, $type));
        } else {
            Mail::to($proposal->getLeader()->email)->send(new $mailClassname($proposal));
        }
        $alias = $proposal->alias()->get();
        foreach ($alias as $userAlias) {
            if (isset($type)) {
                Mail::to($userAlias->email)->send(new $mailClassname($proposal, $type));
            } else {
                Mail::to($userAlias->email)->send(new $mailClassname($proposal));
            }
        }
    }

    protected function sendMailToProposalPartners($proposal, $emailType)
    {
        $mailClassname = "App\\Mail\\" . Str::studly($emailType);
        $partners = $proposal->partners()->where('alias', '0')->get();
        foreach ($partners as $partner) {
            Mail::to($partner->email)->send(new $mailClassname($proposal));
        }
    }

    protected function getLeaderAndAlias($proposal)
    {
        $res = [];
        $leader = $proposal->getLeader();
        $aliases = $proposal->alias()->get();

        $res[$leader->getKey()] = $leader->email;
        foreach ($aliases as $alias) {
            $res[$alias->getKey()] = $alias->email;
        }

        return $res;
    }
}
