<?php

namespace Tests\Unit;

use App\Enums\ProposalStatus;
use App\Enums\ProposalStatusGroups;
use Tests\TestCase;

class ProposalStatusTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testGroups(): void
    {
        $redGroup = ProposalStatusGroups::RED->value;
        $statuses = ProposalStatus::getStatesByGroup($redGroup);


        $this->assertEquals(3, count($statuses));

        $revieableGroup = ProposalStatusGroups::REVIEWABLE->value;
        $statuses = ProposalStatus::getStatesByGroup($revieableGroup);


        $this->assertEquals(4, count($statuses));
        $this->assertEquals(ProposalStatus::SYSTEM_REVIEWERS_CHOSEN->value, $statuses[0]);


    }
}
