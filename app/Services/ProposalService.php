<?php

namespace App\Services;

use App\Enums\ProposalReviewerStatus;
use App\Enums\ProposalStatus;
use App\Models\ApplicantProposal;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\ProposalService as ModelsProposalService;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;

class ProposalService
{

    public static function getItemsForProposalFromCart()
    {
        return ERIHSCartService::getItems();
    }

    public static function getMyClosedProposalQuery($user = null)
    {
        $user = self::getUser();
        if (!$user) {
            return Proposal::where('id', '-1');
        }

        return Proposal::whereHas('leader', function ($query) use ($user) {
            $query->where('applicant_id', $user->id);
        })->orWhereHas('alias', function ($query) use ($user) {
            $query->where('applicant_id', $user->id);
        });
    }

    public static function getMySubmittedProposalQuery($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return Proposal::where('proposals.id', '-1');
        }

        $userId = $user->getKey();

        $applicantsIds = DB::table('applicant_proposal')
            ->where('applicant_id', $userId)
            ->select(['proposal_id'])
            ->get()->pluck('proposal_id', 'proposal_id')->all();

        $servicesIds = DB::table('proposals')
            ->join('proposal_service', 'proposals.id', '=', 'proposal_id')
            ->join('services', 'service_id', '=', 'services.id')
            ->join('service_manager_service', 'service_manager_service.service_id', '=', 'services.id')
            ->where(function ($q2) use ($userId) {
                return $q2->where('user_id', $userId);
            })
            ->where('proposals.status', '!=', ProposalStatus::DRAFT->value)
            ->select(['proposal_id'])
            ->get()->pluck('proposal_id', 'proposal_id')->all();

        $reviewersIds = DB::table('proposal_reviewer')
            ->where('reviewer_id', $userId)
            ->select(['proposal_id'])
            ->get()->pluck('proposal_id', 'proposal_id')->all();

        $ids = $applicantsIds + $servicesIds + $reviewersIds;

        if (count($ids) == 0) {
            return Proposal::where('proposals.id', '-1');
        }
        return Proposal::withDrafts()->whereIn('proposals.id', $ids);
    }

    public static function maxWordsRule($maxWords): array
    {
        return [
            function () use ($maxWords) {
                return function (string $attribute, $value, Closure $fail) use ($maxWords) {
                    if (count(explode(' ', $value)) > $maxWords) {
                        $fail("Max " . $maxWords . " words allowed.");
                    }
                    return true;
                };
            }
        ];
    }

    public static function wordsLeft($maxWords, $state): string
    {
        return 'Max ' . $maxWords . ' words, words left: ' . $maxWords - count(array_filter(explode(' ', trim($state))));
    }

    public static function maxWords($maxWords): string
    {
        return 'Max ' . $maxWords . ' words';
    }

    public static function mySubmittedProposal($user = null)
    {
        $query = self::getMySubmittedProposalQuery($user);
        if ($query) {
            return $query->get();
        }

        return collect([]);
    }

    public static function mySubmittedProposalWithoutDrafts($user = null)
    {
        $query =  self::getMySubmittedProposalQuery($user)->withoutDrafts();
        if ($query) {
            return $query->get();
        }
        return collect([]);
    }

    public static function getProposalFormData($proposal)
    {
        $proposalFormData = $proposal->getAttributes();
        foreach ($proposal->getCasts() as $attribute => $castType) {
            if ($castType === 'array' && isset($proposalFormData[$attribute])) {
                $proposalFormData[$attribute] = json_decode($proposalFormData[$attribute], true);
            }
        }

        return $proposalFormData;
    }

    public static function getItemsForProposalFromDB($proposalId)
    {
        return ModelsProposalService::query()
            ->where('proposal_id', '=', $proposalId)
            ->get();
    }
    public static function getServicesForProposalFromDB($proposalId)
    {
        return Proposal::withDrafts()->find($proposalId)->services;
    }


    public static function getProposalItems(): array
    {
        $itemsData = self::getItemsForProposalFromCart();
        $proposalItems = [];
        foreach ($itemsData as $item) {
            $proposalItem = new ModelsProposalService();
            $proposalItem->service()->associate($item->id);
            $proposalItems[] = $proposalItem;
        }
        return $proposalItems;
    }

    public static function getUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return Auth::user();
    }

    public static function saveLeader($proposal_id): void
    {
        $user = self::getUser();
        $ap = new ApplicantProposal();
        $ap->applicant_id = $user->id;
        $ap->proposal_id = $proposal_id;
        $ap->leader = true;
        $ap->save();
    }
    public static function getApplicantProposalRelationshipValues($relation, $proposal_id): array
    {
        $res['applicant_id'] = $relation['applicant_id'];
        $res['proposal_id'] = $proposal_id;
        $res['created_at'] = now();
        $res['updated_at'] = now();

        return $res;
    }

    public static function getProposalServicesRelationshipValues($relation, $proposal_id): array
    {
        $res['service_id'] = $relation['service_id'];
        $res['proposal_id'] = $proposal_id;
        $res['number_of_days'] = $relation['number_of_days'];
        // $res['first_choice_start_date'] = $relation['first_choice_start_date'];
        // $res['first_choice_end_date'] = $relation['first_choice_end_date'];
        // $res['second_choice_start_date'] = $relation['second_choice_start_date'];
        // $res['second_choice_end_date'] = $relation['second_choice_end_date'];

        return $res;
    }

    public static function getTags($proposal_id)
    {
        $tags = Tag::all();
        return $tags;
    }

    public static function queryGetReviewers($proposal_id)
    {
        $proposal = Proposal::find($proposal_id);
        if ($proposal) {
            $applicants = $proposal->applicants()->pluck('applicant_id')->toArray();
            return User::where('number_of_reviews', '<>', NULL)
                ->where('terms_of_service', '=', '1')
                ->where('confidentiality', '=', '1')
                ->whereHas('roles', function ($query) {
                    $query->where('name', User::REVIEWER_ROLE);
                })->whereNotIn('id', $applicants)
                ->whereDoesntHave('proposalReviewer', function ($query) use ($proposal_id) {
                    $query->where('proposal_id', $proposal_id);
                });
        }
        return null;
    }

    public static function getReviewersFromTags($query, $tags, $tagType): array
    {
        $reviewers = [];
        $userTags = [];

        $users = $query->get();

        if ($users->isEmpty()) {
            return [];
        }

        foreach ($users as $user) {
            $userTags[$user->id] = $user->tagsWithType($tagType)->pluck('id')->toArray();
        }


        foreach ($users as $user) {

            if (array_intersect($tags, $userTags[$user->id])) {
                $reviewers[] = $user;
            }
        }
        return $reviewers;
    }

    public static function getReviewersUsingResearchDisciplines($query, $proposal_id): array
    {
        $proposal = Proposal::find($proposal_id);
        $researchDisciplines = $proposal->tagsWithType('research_disciplines')->pluck('id')->toArray();

        return self::getReviewersFromTags($query, $researchDisciplines, 'research_disciplines');
    }

    public static function getReviewersUsingTechniques($query, $proposal_id)
    {

        $services = Proposal::find($proposal_id)->services()->get();
        $techniques = [];
        foreach ($services as $service) {
            $techniques = array_merge($techniques, ($service->tagsWithType('technique')->pluck('id'))->toArray());
        }

        return self::getReviewersFromTags($query, $techniques, 'technique');
    }

    public static function getReviewersUsingMaterials($query, $proposal_id)
    {
        $proposal = Proposal::find($proposal_id);
        $materials = [];
        if ($proposal) {
            $molabObjectsData = $proposal->molab_objects_data;
            if ($molabObjectsData) {
                foreach ($molabObjectsData as $item) {
                    if ($item['molab_object_material']) {
                        $materials = array_merge($materials, $item['molab_object_material']);
                    }
                }
            }

            $fixlabObjectData = $proposal->fixlab_objects_data;
            if ($fixlabObjectData) {
                foreach ($fixlabObjectData as $item) {
                    if ($item['fixlab_object_material']) {
                        $materials = array_merge($materials, $item['fixlab_object_material']);
                    }
                }
            }
        }

        return self::getReviewersFromTags($query, collect($materials)->pluck('id')->toArray(), 'material');
    }

    public static function getReviewersUsingObjectTypes($query, $proposal_id)
    {
        $proposal = Proposal::find($proposal_id);
        $types = [];
        if ($proposal) {
            $molabObjectsData = $proposal->molab_objects_data;
            if ($molabObjectsData) {
                foreach ($molabObjectsData as $item) {
                    $types = array_merge($types, $item['molab_object_type']);
                }
            }

            $fixlabObjectData = $proposal->fixlab_objects_data;
            if ($fixlabObjectData) {
                foreach ($fixlabObjectData as $item) {
                    $types = array_merge($types, $item['fixlab_object_type']);
                }
            }
        }

        $reviewers = [];

        $users = $query->get();

        foreach ($users as $user) {
            if (UserService::userHasObjectType($user, $types)) {
                $reviewers[] = $user;
            }
        }
        return $reviewers;
    }

    public static function assignReviewers($proposal_id)
    {
        $query = self::queryGetReviewers($proposal_id);
        $proposal = Proposal::find($proposal_id);
        if ($query) {
            $reviewers = self::getReviewersUsingResearchDisciplines($query, $proposal_id);
            $reviewers = array_merge($reviewers, self::getReviewersUsingObjectTypes($query, $proposal_id));
            $reviewers = array_merge($reviewers, self::getReviewersUsingMaterials($query, $proposal_id));
            $reviewers = array_merge($reviewers, self::getReviewersUsingTechniques($query, $proposal_id));
            $numberOfReviewers = sizeof($reviewers);
            if ($numberOfReviewers == 0) {
                (new ERIHSMailService())->noReviewersAvailable($proposal);
            } else {
                $randomKeys = $numberOfReviewers > 3 ? array_rand($reviewers, 3) : array_keys($reviewers);
                foreach ($randomKeys as $index) {
                    $reviewer_id = $reviewers[$index]->id;
                    $proposalReviewer = new ProposalReviewer();
                    $proposalReviewer->proposal_id = $proposal_id;
                    $proposalReviewer->reviewer_id = $reviewer_id;
                    $proposalReviewer->status = ProposalReviewerStatus::TO_BE_CONFIRMED->name;
                    $proposalReviewer->save();
                }
                (new ERIHSMailService())->confirmReviewer($proposal);
            }
        }
    }

    public static function blockReviewersAfterRanking($proposal_id)
    {
        $proposal = Proposal::find($proposal_id);
        $proposal->reviewers()
            ->where('status', ProposalReviewerStatus::WAITING->name)
            ->orWhere('status', ProposalReviewerStatus::TO_BE_CONFIRMED->name)
            ->update(['status' => ProposalReviewerStatus::SKIPPED->name]);
    }

    public static function getMolabObjectTypes(): array
    {
        return [
            'Artwork(s)' => 'Artwork(s)',
            'Monument(s)' => 'Monument(s)',
            'Sample(s)' => 'Sample(s)',
            'Archaeological site(s)' => 'Archaeological site(s)'
        ];
    }

    public static function getFixlabObjectTypes(): array
    {
        return [
            'Object(s)' => 'Object(s)',
            'Sample(s)' => 'Sample(s)',
        ];
    }

    public static function getAllObjectTypes(): array
    {
        return array_merge(self::getMolabObjectTypes(), self::getFixlabObjectTypes());
    }

    public static function canSubmitProposal()
    {
        $user = \auth()->user();
        if (!CallService::getOpenCalls()) {
            return ['can_open' => false, 'motivation' => 'no_open_calls'];
        }

        //Controllo se l'utente non ha già una proposal scritta
        if (!$user) {
            return ['can_open' => false, 'motivation' => 'user_not_logged'];
        }
        $has_proposal_opened = Proposal::where('publisher_id', '=', $user->id)
            ->where('status', '!=', ProposalStatus::ARCHIVED)
            ->where('status', '!=', ProposalStatus::SECOND_DRAFT)
            ->first();
        if ($has_proposal_opened) {
            return ['can_open' => false, 'motivation' => 'proposal_already_opened'];
        }

        $has_proposal_in_draft = Proposal::where('publisher_id', '=', $user->id)
            ->where('status', '=', ProposalStatus::DRAFT)
            ->where('status', '!=', ProposalStatus::SECOND_DRAFT)
            ->first();
        if ($has_proposal_in_draft) {
            return ['can_open' => false, 'motivation' => 'proposal_in_draft'];
        }

        $has_proposal_without_post_access_duties = Proposal::where('publisher_id', '=', $user->id)
            ->where('status', '=', ProposalStatus::MISSED_POST_ACCESS_DUTIES_DEADLINE)
            ->first();

        if ($has_proposal_without_post_access_duties) {
            return ['can_open' => false, 'motivation' => 'missed_post_access_duties'];
        }

        return ['can_open' => true];
    }

    public static function getRankNotes($proposal)
    {
        $history = $proposal->getApplicationHistory();
        foreach ($history as $history_item) {
            if (
                $history_item['status_code'] == ProposalStatus::RANKED_MAIN_LIST->value
                || $history_item['status_code'] == ProposalStatus::RANKED_RESERVE_LIST->value
            ) {
                return $history_item['info']['msg'] ?? '';
            }
        }
    }
}
