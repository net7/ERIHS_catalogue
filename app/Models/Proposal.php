<?php

namespace App\Models;

use App\Enums\ProposalReviewerStatus;
use App\Enums\ProposalStatus;
use App\Enums\ProposalStatusGroups;
use App\Services\ERIHSMailService;
use App\Models\ProposalService;
use App\Traits\FSMTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;
use Spatie\Permission\Models\Role;
use Spatie\Tags\HasTags;

use Kenepa\ResourceLock\Models\Concerns\HasLocks;

class Proposal extends Model
{
    use HasFactory;
    use HasTags;
    use HasDrafts;
    use FSMTrait;
    use HasLocks;


    protected $guarded = [
        'id',
    ];
    protected $casts = [
        // 'project_questions' => 'array',
        // 'previous_relevant_analysis' => 'array',
        // 'project_method_statements' => 'array',
        // 'project_impacts' => 'array',
        // 'date' => 'array',
        // 'type_of_object' => 'array',
        // 'ownership_consent' => 'array',
        //  'drone_flight' => 'array',
        'molab_objects_data' => 'array',
        // 'molab_drone_flight_file' => 'array',
        // 'molab_x_ray_file' => 'array',
        'fixlab_objects_data' => 'array',
        'social_challenges' => 'array',
        'archlab_type' => 'array'


    ];

    protected array $draftableRelations = [
        'applicants',
        'leader',
        'alias',
        'proposalTools',
        'proposalServices',
        'services',
        'tools',
        'tags'
    ];


    public function call()
    {
        return $this->belongsTo(Call::class);
    }

    public function evaluations()
    {
        return $this->hasMany(ProposalEvaluation::class);
    }

    public function applicantProposals(): HasMany
    {
        return $this->hasMany(ApplicantProposal::class);
    }

    public function applicants()
    {
        return $this->belongsToMany(User::class, 'applicant_proposal', 'proposal_id', 'applicant_id')->withPivot('leader', 'alias');
    }

    public function leader()
    {
        return $this->applicants()->wherePivot('leader', 1);
    }

    public function alias()
    {
        return $this->applicants()->wherePivot('alias', 1);
    }

    public function partners()
    {
        return $this->applicants()->wherePivot('leader', 0);
    }


    public function proposalServices(): HasMany
    {
        return $this->hasMany(ProposalService::class, 'proposal_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withPivot('feasible');
    }


    public function servicesByServiceManager($userId)
    {
        return $this->services()
            ->whereHas('serviceManagers',
             function($query) use ($userId){
                $query->where('user_id', $userId);
            }
        );
    }


    public function reviewers()
    {
        return $this->hasMany(ProposalReviewer::class, 'proposal_id');
    }

    public function postDutiesReport()
    {
        return $this->hasOne(PostAccessReport::class, 'proposal_id');
    }

    public function ribbonColour()
    {

        return $this->getStatusColor($this->status)['group'];
    }

    public function attachments()
    {
        return $this->hasMany(ProposalAttachment::class, 'proposal_id');
    }

    public function getOrganizationsUsers()
    {

        $organizationsUsers = [];

        foreach ($this->proposalServices as $proposalService) {
            $service = $proposalService->service;
            $organizationsUsers[$service->getKey()] = [
                'service' => $service,
                'organization' => $service->organization,
                'organization_users' => $service->organization->users,
            ];
        }

        return $organizationsUsers;
    }

    public function getApplicationHistory($activitiesDataCallback = null, $activitiesDataCallbackParams = [], $reverse = false)
    {

        $fullHistory = $this->getStatusHistoryWithActivities($activitiesDataCallback, $activitiesDataCallbackParams, $reverse);

        if ($reverse) {
            return array_reverse($fullHistory);
        }
        return $fullHistory;
    }

    public function getStatusColor($status)
    {

        if ($this->fsm->isInGroup($status, 'green')) {

            return [
                'group' => 'green',
                'color' => '#22C55E'
            ];
        }
        if ($this->fsm->isInGroup($status, 'yellow')) {
            return [
                'group' => 'yellow',
                'color' => '#F59E0B'
            ];
        }
        if ($this->fsm->isInGroup($status, 'red')) {
            return [
                'group' => 'red',
                'color' => '#f10d25',
            ];
        }
        return [
            'group' => 'gray',
            'color' => '#9CA3AF',
        ];
    }

    public function feasibilityResponse()
    {
        $proposalServices =
            $this->proposalServices;

        $allFeasible = true;
        $allNotFeasible = true;
        foreach ($proposalServices as $proposalService) {
            if (!$proposalService->hasFeasibility()) {
                return false;
            }
            if ($proposalService->isNotFeasible()) {
                $allFeasible = false;
            } else {
                $allNotFeasible = false;
            }
        }

        if ($allFeasible) {
            return ProposalStatus::FEASIBLE;
        }

        // TODO: what if the call has been closed meanwhile?
        if ($this->hasBeenInStatus(ProposalStatus::SECOND_DRAFT->value)) {
            return $allNotFeasible ? ProposalStatus::SECOND_NOT_FEASIBLE : ProposalStatus::SECOND_PARTIALLY_FEASIBLE;
        }

        return $allNotFeasible ? ProposalStatus::NOT_FEASIBLE : ProposalStatus::PARTIALLY_FEASIBLE;
    }

    public function isCarriedOut()
    {
        $proposalServices =
            $this->proposalServices;

        foreach ($proposalServices as $proposalService) {
            if ($proposalService->access != ProposalService::ACCESS_CARRIED_OUT) {
                return false;
            }
        }

        return true;
    }

    public function isAccessScheduled()
    {
        $proposalServices =
            $this->proposalServices;

        foreach ($proposalServices as $proposalService) {
            if (!$proposalService->access == ProposalService::ACCESS_SCHEDULED) {
                return false;
            }
        }

        return true;
    }

    public function proposalServiceOfService($serviceId)
    {
        return $this->proposalServices()
            ->where('service_id', $serviceId)
            ->first();
    }

    public function isInFirstDraft()
    {
        return $this->{$this->getStatusFieldname()} == ProposalStatus::DRAFT->value;
    }

    public function isInSecondDraft()
    {
        return $this->{$this->getStatusFieldname()} == ProposalStatus::SECOND_DRAFT->value;
    }

    public function resetProposalServices()
    {
        foreach ($this->proposalServices as $proposalService) {
            // we reset only the non feasible ones
            if ($proposalService->feasible == ProposalService::NOT_FEASIBLE) {
                $proposalService->feasible = ProposalService::TO_BE_DEFINED;
                $proposalService->motivation = null;
                $proposalService->save();
            }
        }
    }

    public function reviewersAcceptanceResponse()
    {

        // TODO: e se non ci sono 3 reviewer, ma i due hanno dato il voto e l'help desk  da' l'ok?
        $reviewers =
            $this->reviewers()->where('status', ProposalReviewerStatus::ACCEPTED->name)
            ->get();

        if ($reviewers->count() >= config('app.min_reviewers')) {
            return true;
        }

        return false;
    }

    public function isFirstReviewerConfirmed()
    {
        $reviewers =
            $this->reviewers;

        $nWaiting = 0;
        foreach ($reviewers as $reviewer) {
            switch ($reviewer->status) {
                case ProposalReviewerStatus::TO_BE_CONFIRMED->name:
                    break;
                case ProposalReviewerStatus::WAITING->name:
                    $nWaiting++;
                    break;
                default:
                    return false;
            }
        }

        if ($nWaiting < 2) {
            return true;
        }
        return false;
    }

    public function canBeRanked($user = null)
    {
        if (is_null($user)) {
            $user = Auth::user();
        }
        if (!$user) {
            return false;
        }
        if ($this->evaluations->count() < 2) {
            return false;
        }

        if ($this->call->isOpen()) {
            return false;
        }

        if (
            $this->status == ProposalStatus::RANKED_RESERVE_LIST->value
            && $user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE])
        ) {
            return true;
        }

        if ($this->hasBeenInAnyStatus(
            [
                ProposalStatus::RANKED_MAIN_LIST->value,
                ProposalStatus::RANKED_RESERVE_LIST->value,
                ProposalStatus::RANKED_BELOW_THRESHOLD->value,
            ]
        )) {
            return false;
        }

        return true;
    }

    public function rank($result, $resultNotes = null)
    {

        $info = $resultNotes ? ['msg' => $resultNotes] : [];
        $this->makeTransitionAndSave($result, $info);
    }

    public function isAcceptable()
    {
        return $this->status == ProposalStatus::RANKED_MAIN_LIST->value;
    }

    public function isDiscardable()
    {
        return $this->isCurrentlyInGroup(ProposalStatusGroups::CAN_BE_DISCARDED->value);
    }

    public function isConfirmable()
    {
        return $this->isCurrentlyInGroup(ProposalStatusGroups::CAN_BE_CONFIRMED->value);
    }

    public function canBeAcceptedBy(User $user = null)
    {
        if (!$this->isAcceptable()) {
            return false;
        }

        if (!$user) {
            $user = Auth::user();
        }

        if ($this->isUserLeaderOrAlias($user)) {
            return true;
        }

        if ($user && $user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        return false;
    }
    public function canServiceBeRemoved(Service $service, User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        if (!$user) {
            return false;
        }

        if (!Auth::user()->can('administer proposals')) {
            return false;
        }

        if ($service->isScheduledOrCarriedOut(proposal_id: $this->id)) {
            Log::debug("canServiceBeRemoved returning true ");
            return false;
        }

        return true;
    }

    public function canBeDiscarded(User $user = null)
    {
        if (!$this->isDiscardable()) {
            return false;
        }

        if (!$user) {
            $user = Auth::user();
        }

        if ($user->hasRole(User::HELP_DESK_ROLE) || $user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        return false;
    }

    public function canBeConfirmed(User $user = null)
    {
        if (!$this->isConfirmable()) {
            return false;
        }

        if (!$user) {
            $user = Auth::user();
        }

        if ($user->hasRole(User::HELP_DESK_ROLE) || $user->hasRole(User::ADMIN_ROLE)) {
            return true;
        }

        return false;
    }

    public function getLeaderId()
    {
        $leader = $this->leader()->first();
        return $leader?->getKey();
    }

    public function accept()
    {
        $this->makeTransitionAndSave(ProposalStatus::ACCEPTED->value);
    }

    public function discard($resultNotes)
    {
        $info = $resultNotes ? ['msg' => $resultNotes] : [];
        $this->makeTransitionAndSave(ProposalStatus::ARCHIVED->value, $info);
        (new ERIHSMailService())->proposalDiscarded($this);
    }

    public function confirm($resultNotes)
    {
        $info = $resultNotes ? ['msg' => $resultNotes] : [];
        $this->makeTransitionAndSave(ProposalStatus::FILES_CONFIRMED->value, $info);
        (new ERIHSMailService())->proposalConfirmed($this);
    }

    public function isUserLeader($user): bool
    {
        if (!$user) {
            $user = Auth::user();
        }
        if ($this->leader->first()?->id == $user->getKey()) {
            return true;
        }
        return false;
    }

    public function isUserAlias($user): bool
    {
        if (!$user) {
            $user = Auth::user();
        }
        if (in_array($user->getKey(), $this->alias()->get()->pluck('id')->toArray())) {
            return true;
        }
        return false;
    }

    public function isUserLeaderOrAlias($user): bool
    {
        return $this->isUserLeader($user) || $this->isUserAlias($user);
    }

    public function accessScheduled()
    {
        $this->makeTransitionAndSave(ProposalStatus::ACCESS_SCHEDULED->value);
    }

    public function getLeader()
    {
        return $this->leader()->first();
    }

    // This is used by FSMTrait
    public function setStatusSystemReviewersChosen($prevStatusCode = null, $statusData = null, $params = null)
    {
        \App\Services\ProposalService::assignReviewers($this->id);
    }

    public function getPlatforms()
    {
        $platforms = collect([]);
        foreach ($this->services()->get() as $service) {
            $platforms = $platforms->merge($service->getPlatforms());
        }
        return $platforms;
    }
  
    public function getResearchDisciplines()
    {
        $result = [];
        $disciplines = $this->tagsWithType('research_disciplines')->all();
        foreach ($disciplines as $discipline) {
            $result[] = $discipline->name;
        }
        return $result;
    }
}
