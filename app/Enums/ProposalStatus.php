<?php

namespace App\Enums;

use App\Traits\EnumHelper;
use Gecche\FSM\Contracts\FSMConfigInterface;

enum ProposalStatus: string implements FSMConfigInterface
{
    use EnumHelper;

    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case FEASIBLE = 'feasible';
    case PARTIALLY_FEASIBLE = 'partially_feasible';
    case NOT_FEASIBLE = 'not_feasible';
    case SECOND_DRAFT = 'second_draft';
    case RESUBMITTED = 'resubmitted';
    case SECOND_PARTIALLY_FEASIBLE = 'second_partially_feasible';
    case SECOND_NOT_FEASIBLE = 'second_not_feasible';
    case SYSTEM_REVIEWERS_CHOSEN = 'system_reviewers_chosen';
    case UH_REVIEWERS_MANAGING = 'uh_reviewers_managing';
    case REVIEWERS_CHANGED = 'reviewers_changed';
    case UNDER_REVIEW = 'under_review';
    case RANKED = 'ranked';
    case RANKED_BELOW_THRESHOLD = 'ranked_below_threshold';
    case RANKED_RESERVE_LIST = 'ranked_reserve_list';
    case RANKED_MAIN_LIST = 'ranked_main_list';
    case ACCEPTED = 'accepted';
    case ACCESS_SCHEDULED = 'access_scheduled';
    case ACCESS_CLOSED = 'access_closed';
    case POST_ACCESS_DUTIES_DONE = 'post_access_duties_done';
    case MISSED_POST_ACCESS_DUTIES_DEADLINE = 'missed_post_access_duties_deadline';
    case ARCHIVED = 'archived';
    case CARRIED_OUT = 'carried_out';
    case FILES_CONFIRMED = 'files_confirmed';


    public static function getStatesByGroup($group)
    {
        $states = [];
        foreach (self::states() as $stateName => $stateData) {

            if (@in_array($group, $stateData['groups'])) {
                $states[] = $stateName;
            }
        }

        return $states;
    }

    public static function states()
    {
        return [
            self::DRAFT->value => [
                'groups' => [
                    ProposalStatusGroups::DELETABLE->value,
                    ProposalStatusGroups::YELLOW->value,
                    ProposalStatusGroups::DRAFT->value,
                    ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value
                ],
                'description' => 'Draft',
            ],
            self::SUBMITTED->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::IN_FEASIBILITY->value,
                    ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value
                ],
                'description' => 'Submitted',
            ],
            self::FEASIBLE->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value
                ],
            ],
            self::PARTIALLY_FEASIBLE->value => [
                'groups' => [
                    ProposalStatusGroups::YELLOW->value,
                    ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value
                ],
            ],
            self::NOT_FEASIBLE->value => [
                'groups' => [
                    ProposalStatusGroups::RED->value
                ],
                'description' => 'Not feasible',
            ],

            self::SECOND_DRAFT->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::DRAFT->value,
                    ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value
                ],
            ],
            self::RESUBMITTED->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::IN_FEASIBILITY->value,
                    ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value
                ],
            ],

            self::SECOND_PARTIALLY_FEASIBLE->value => [
                'groups' => [
                    ProposalStatusGroups::YELLOW->value,
                    ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value
                ],
                'description' => 'Partially Feasible (2nd round)',
            ],
            self::SECOND_NOT_FEASIBLE->value => [
                'groups' => [
                    ProposalStatusGroups::RED->value
                ],
                'description' => 'Not Feasible (2nd round)',
            ],

            self::SYSTEM_REVIEWERS_CHOSEN->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::REVIEWABLE->value
                ],
            ],
            self::UH_REVIEWERS_MANAGING->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::REVIEWABLE->value
                ],
                'description' => 'Managing reviewers by UH users',
            ],
            self::REVIEWERS_CHANGED->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::REVIEWABLE->value
                ],
            ],

            self::UNDER_REVIEW->value => [
                'groups' => [
                    ProposalStatusGroups::YELLOW->value,
                    ProposalStatusGroups::REVIEWABLE->value
                ],
            ],

            self::RANKED->value => [
                'groups' => [ProposalStatusGroups::GREEN->value],
            ],
            self::RANKED_RESERVE_LIST->value => [
                'description' => "PROPOSAL RANKED IN RESERVE LIST",
                'groups' => [ProposalStatusGroups::YELLOW->value],
            ],
            self::RANKED_MAIN_LIST->value => [
                'description' => "PROPOSAL RANKED IN MAIN LIST",
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::CAN_BE_DISCARDED->value
                ],
            ],
            self::RANKED_BELOW_THRESHOLD->value => [
                'description' => "PROPOSAL RANKED BELOW THRESHOLD",
                'groups' => [ProposalStatusGroups::RED->value],
            ],

            self::ACCEPTED->value => [
                'groups' => [
                    ProposalStatusGroups::GREEN->value,
                    ProposalStatusGroups::CAN_BE_DISCARDED->value,
                    ProposalStatusGroups::CAN_BE_CONFIRMED->value
                ],
            ],

            self::FILES_CONFIRMED->value => [
                'groups' => [ProposalStatusGroups::GREEN->value],
            ],

            self::CARRIED_OUT->value => [
                'groups' => [ProposalStatusGroups::GREEN->value],
            ],

            self::ACCESS_SCHEDULED->value => [
                'groups' => [ProposalStatusGroups::GREEN->value],
            ],
            self::ACCESS_CLOSED->value => [
                'groups' => [ProposalStatusGroups::GREEN->value],
            ],
            self::POST_ACCESS_DUTIES_DONE->value => [
                'groups' => [ProposalStatusGroups::GREEN->value],
            ],

            self::ARCHIVED->value => [
                'final' => true,
                'groups' => [
                    ProposalStatusGroups::GRAY->value,
                    ProposalStatusGroups::FINAL->value
                ],
            ],
        ];
    }

    public static function root()
    {
        return self::DRAFT->value;
    }

    public static function groups()
    {
        return [
            ProposalStatusGroups::DELETABLE->value => "The proposal can be deleted",
            ProposalStatusGroups::GREEN->value => "The status is in a green status",
            ProposalStatusGroups::YELLOW->value  => "The status is in a yellow status",
            ProposalStatusGroups::RED->value => "The status is in a red status",
            ProposalStatusGroups::GRAY->value => "The status is in a gray status",
            ProposalStatusGroups::DRAFT->value => "The status is in a draft status",
            ProposalStatusGroups::IN_FEASIBILITY->value => 'The proposal can be evaluated for feasibility',
            ProposalStatusGroups::REVIEWABLE->value => 'The proposal can be reviewed',
            ProposalStatusGroups::CAN_BE_EDITED_BY_HELP_DESK->value => 'The proposal can be edited by the user help desk',
            ProposalStatusGroups::CAN_BE_DISCARDED->value => 'The proposal can be discarded by the user help desk',
            ProposalStatusGroups::CAN_BE_CONFIRMED->value => 'The proposal can be confirmed by the user help desk',
            ProposalStatusGroups::FINAL->value => 'The process has ended'

        ];
    }

    public static function transitions()
    {
        return [
            self::DRAFT->value => [
                self::SUBMITTED->value,
            ],
            self::SUBMITTED->value => [
                self::FEASIBLE->value,
                self::PARTIALLY_FEASIBLE->value,
                self::NOT_FEASIBLE->value,
            ],
            self::PARTIALLY_FEASIBLE->value => [
                self::SECOND_DRAFT->value,
            ],
            self::NOT_FEASIBLE->value => [
                self::SECOND_DRAFT->value,
            ],
            self::SECOND_DRAFT->value => [
                self::RESUBMITTED->value,
                self::ARCHIVED->value,
            ],

            self::RESUBMITTED->value => [
                self::FEASIBLE->value,
                self::SECOND_PARTIALLY_FEASIBLE->value,
                self::SECOND_NOT_FEASIBLE->value,
            ],

            self::SECOND_PARTIALLY_FEASIBLE->value => [
                self::ARCHIVED->value,
            ],
            self::SECOND_NOT_FEASIBLE->value => [
                self::ARCHIVED->value,
            ],

            self::FEASIBLE->value => [
                self::RANKED_RESERVE_LIST->value,
                self::RANKED_MAIN_LIST->value,
                self::RANKED_BELOW_THRESHOLD->value,
                self::SYSTEM_REVIEWERS_CHOSEN->value,
            ],
            self::SYSTEM_REVIEWERS_CHOSEN->value => [
                self::RANKED_RESERVE_LIST->value,
                self::RANKED_MAIN_LIST->value,
                self::RANKED_BELOW_THRESHOLD->value,
                self::UNDER_REVIEW->value,
                self::REVIEWERS_CHANGED->value,
                self::ARCHIVED->value,
            ],

            self::UH_REVIEWERS_MANAGING->value => [
                self::UNDER_REVIEW->value,
                self::REVIEWERS_CHANGED->value,
                self::ARCHIVED->value,
            ],
            self::REVIEWERS_CHANGED->value => [
                self::REVIEWERS_CHANGED->value,
                self::ARCHIVED->value,
                self::UH_REVIEWERS_MANAGING->value
            ],
            self::UNDER_REVIEW->value => [
                self::RANKED_RESERVE_LIST->value,
                self::RANKED_MAIN_LIST->value,
                self::RANKED_BELOW_THRESHOLD->value,
            ],
            self::RANKED_BELOW_THRESHOLD->value => [
                self::ARCHIVED->value,
            ],
            self::RANKED_RESERVE_LIST->value => [
                self::ACCEPTED->value,
                self::ARCHIVED->value,
                self::RANKED_MAIN_LIST->value,
            ],
            self::RANKED_MAIN_LIST->value => [
                self::ACCEPTED->value,
                self::ARCHIVED->value,
            ],

            self::ACCEPTED->value => [
                self::ACCESS_CLOSED->value,
                self::ARCHIVED->value,
                self::FILES_CONFIRMED->value,
            ],

            self::FILES_CONFIRMED->value => [
                self::ACCESS_SCHEDULED->value,
                self::ACCESS_CLOSED->value
            ],

            self::ACCESS_SCHEDULED->value => [
                self::ACCESS_CLOSED->value,
                self::ARCHIVED->value,
                self::CARRIED_OUT->value,
            ],
            self::ACCESS_CLOSED->value => [
                self::POST_ACCESS_DUTIES_DONE->value,
            ],

            self::POST_ACCESS_DUTIES_DONE->value => [
                self::ARCHIVED->value,
            ],

            self::ARCHIVED->value => [],
        ];
    }

    public static function myProposalGroupStatuses()
    {

        return [
            self::DRAFT->value,
            self::SUBMITTED->value,
            self::FEASIBLE->value,
            self::PARTIALLY_FEASIBLE->value,
            self::NOT_FEASIBLE->value,
            self::SECOND_DRAFT->value,
            self::RESUBMITTED->value,
            self::SECOND_PARTIALLY_FEASIBLE->value,
            self::SECOND_NOT_FEASIBLE->value,
            self::SYSTEM_REVIEWERS_CHOSEN->value,
            self::UH_REVIEWERS_MANAGING->value,
            self::REVIEWERS_CHANGED->value,
            self::UNDER_REVIEW->value,
            self::RANKED->value,
            self::RANKED_BELOW_THRESHOLD->value,
            self::RANKED_RESERVE_LIST->value,
            self::RANKED_MAIN_LIST->value,
            self::ACCEPTED->value,
            self::ACCESS_SCHEDULED->value,
            self::ACCESS_CLOSED->value,
            self::POST_ACCESS_DUTIES_DONE->value,
        ];
    }
}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
