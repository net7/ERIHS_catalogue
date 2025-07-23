<?php
return [

    'models' => [
        \App\Models\Proposal::class => 'proposal',
        \App\Models\MyProposal::class => 'proposal',
    ],

    'listener' => \App\Listeners\HandleStatusTransition::class,

    'models_listeners' => [
        \App\Models\Proposal::class => \App\Listeners\HandleProposalStatusTransition::class,
        \App\Models\MyProposal::class => \App\Listeners\HandleProposalStatusTransition::class,
    ],

    'types' => [
        'proposal' => [
            'fsmConfigClass' => \App\Enums\ProposalStatus::class,
        ],
    ],

];
