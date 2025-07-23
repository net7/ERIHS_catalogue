<?php

namespace Tests\Unit;

use App\Enums\ProposalStatus;
use App\Services\StatisticsService;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Call;
use App\Models\Service;
use App\Models\Tool;
use App\Models\Method;
use App\Models\Tag;
use App\Services\ERIHSMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Tags\Tag as TagsTag;
use Tests\TestCase;
use Mockery\MockInterface;

class StatisticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $statisticsService;

    public function setUp(): void
    {
        parent::setUp();
        $this->statisticsService = new StatisticsService();
    }

    public function testGetTotalSubmittedProposals()
    {
        Proposal::factory()->count(10)->create();
        $totalSubmitted = $this->statisticsService->getTotalSubmittedProposals();
        $this->assertEquals(10, $totalSubmitted);
    }

    public function testGetSubmittedProposalsByCountry()
    {
        $usersA = User::factory()->count(5)->create(['country' => 'CountryA']);
        $usersB = User::factory()->count(5)->create(['country' => 'CountryB']);

        Proposal::factory()->count(5)->configure()->create()->each(function ($proposal) use ($usersA) {
            $proposal->applicants()->attach($usersA->random(), ['leader' => true, 'alias' => false]);
        });

        Proposal::factory()->count(5)->configure()->create()->each(function ($proposal) use ($usersB) {
            $proposal->applicants()->attach($usersB->random(), ['leader' => true, 'alias' => false]);
        });

        $submittedByCountry = $this->statisticsService->getSubmittedProposalsByCountry();



        $this->assertCount(2, $submittedByCountry);

        // foreach ($submittedByCountry as $item) {
        //     dd($item);
        // }
        $this->assertEquals(5, $submittedByCountry->where('country', 'CountryA')->first()->total);
        $this->assertEquals(5, $submittedByCountry->where('country', 'CountryB')->first()->total);
    }

    public function testGetTotalAcceptedProposals()
    {
        // create admin user
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->mock(ERIHSMailService::class, function (MockInterface $mock) {
            $mock->shouldReceive('applicationAccepted')->times(7);
        });

        $proposals = Proposal::factory()->count(7)->create(['status' => ProposalStatus::RANKED_MAIN_LIST->value]);
        foreach ($proposals as $proposal) {
            $proposal->makeTransition(ProposalStatus::ACCEPTED->value);
            $proposal->save();
        }

        $totalAccepted = $this->statisticsService->getTotalAcceptedProposals();
        $this->assertEquals(7, $totalAccepted);
    }

    public function testGetAcceptedProposalsByCountry()
    {
        $usersA = User::factory()->count(3)->create(['country' => 'CountryA']);
        $usersB = User::factory()->count(4)->create(['country' => 'CountryB']);

        Proposal::factory()->count(3)->configure()->create(['status' => 'ranked_main_list'])->each(function ($proposal) use ($usersA) {
            $proposal->applicants()->attach($usersA->random(), ['leader' => true, 'alias' => false]);
        });

        Proposal::factory()->count(4)->configure()->create(['status' => 'ranked_main_list'])->each(function ($proposal) use ($usersB) {
            $proposal->applicants()->attach($usersB->random(), ['leader' => true, 'alias' => false]);
        });

        $acceptedByCountry = $this->statisticsService->getAcceptedProposalsByCountry();

        $this->assertCount(2, $acceptedByCountry);
        $this->assertEquals(3, $acceptedByCountry->where('country', 'CountryA')->first()->total);
        $this->assertEquals(4, $acceptedByCountry->where('country', 'CountryB')->first()->total);
    }

    public function testGetAcceptanceRate()
    {
        Proposal::factory()->count(10)->create(['status' => 'ranked_main_list']);
        Proposal::factory()->count(5)->create(['status' => 'ranked_below_threshold']);

        $acceptanceRate = $this->statisticsService->getAcceptanceRate();
        $this->assertEquals(10/15*100, $acceptanceRate);
    }

    public function testGetProposalsByGender()
    {
        $usersMale = User::factory()->count(6)->create(['gender' => 'male']);
        $usersFemale = User::factory()->count(4)->create(['gender' => 'female']);

        Proposal::factory()->count(6)->configure()->create()->each(function ($proposal) use ($usersMale) {
            $proposal->applicants()->attach($usersMale->random(), ['leader' => true, 'alias' => false]);
        });

        Proposal::factory()->count(4)->configure()->create()->each(function ($proposal) use ($usersFemale) {
            $proposal->applicants()->attach($usersFemale->random(), ['leader' => true, 'alias' => false]);
        });

        $proposalsByGender = $this->statisticsService->getProposalsByGender();

        $this->assertCount(2, $proposalsByGender);
        $this->assertEquals(6, $proposalsByGender->where('gender', 'male')->first()->total);
        $this->assertEquals(4, $proposalsByGender->where('gender', 'female')->first()->total);
    }

    public function testGetProposalsByType()
    {
        Proposal::factory()->count(8)->create(['type' => 'NEW']);
        Proposal::factory()->count(2)->create(['type' => 'LONG_TERM_PROJECT']);
        Proposal::factory()->count(5)->create(['type' => 'RESUBMISSION']);

        $proposalsByType = $this->statisticsService->getProposalsByType();

        $this->assertCount(3, $proposalsByType);
        $this->assertEquals(8, $proposalsByType->where('type', 'NEW')->first()->total);
        $this->assertEquals(2, $proposalsByType->where('type', 'LONG_TERM_PROJECT')->first()->total);
        $this->assertEquals(5, $proposalsByType->where('type', 'RESUBMISSION')->first()->total);
    }

    public function testGetProposalsByStatus()
    {
        Proposal::factory()->count(5)->create(['status' => 'submitted']);
        Proposal::factory()->count(3)->create(['status' => 'accepted']);
        // Proposal::factory()->count(2)->create(['status' => 'rejected']);
        Proposal::factory()->count(2)->create(['status' => 'archived']);

        $proposalsByStatus = $this->statisticsService->getProposalsByStatus();

        $this->assertCount(3, $proposalsByStatus);
        $this->assertEquals(5, $proposalsByStatus->where('status', 'submitted')->first()->total);
        $this->assertEquals(3, $proposalsByStatus->where('status', 'accepted')->first()->total);
        // $this->assertEquals(2, $proposalsByStatus->where('status', 'rejected')->first()->total);
        $this->assertEquals(2, $proposalsByStatus->where('status', 'archived')->first()->total);
    }

    public function testGetProposalsByDiscipline()
    {
        // Create tags for disciplines
        $disciplineATag = TagsTag::findOrCreate('DisciplineA', 'research_disciplines');
        $disciplineBTag = TagsTag::findOrCreate('DisciplineB', 'research_disciplines');



        // Create services and attach disciplines via tags
        $serviceA = Service::factory()->create();
        $serviceA->attachTag($disciplineATag);

        $serviceB = Service::factory()->create();
        $serviceB->attachTag($disciplineBTag);

        // Create proposals and associate them with services
        Proposal::factory()->count(4)->create()->each(function ($proposal) use ($serviceA) {
            $proposal->services()->attach($serviceA);
            // $proposal->save();
        });
        Proposal::factory()->count(6)->create()->each(function ($proposal) use ($serviceB) {
            $proposal->services()->attach($serviceB);
            // $proposal->save();
        });

        $proposalsByDiscipline = $this->statisticsService->getProposalsByDiscipline();


        $filteredDisciplineA = $proposalsByDiscipline->filter(function ($item) {
            $domain = json_decode($item->research_disciplines, true); // Convert JSON string to an array
            return isset($domain['en']) && $domain['en'] === 'DisciplineA';
        });

        $filteredDisciplineB = $proposalsByDiscipline->filter(function ($item) {
            $domain = json_decode($item->research_disciplines, true); // Convert JSON string to an array
            return isset($domain['en']) && $domain['en'] === 'DisciplineB';
        });

        $this->assertCount(2, $proposalsByDiscipline);

        $this->assertEquals(4, $filteredDisciplineA->first()->total);
        $this->assertEquals(6, $filteredDisciplineB->first()->total);
    }

    public function testGetAcceptedProposalsByDiscipline()
    {
        // Create tags for disciplines
        $disciplineATag = TagsTag::findOrCreate('DisciplineA', 'research_disciplines');
        $disciplineBTag = TagsTag::findOrCreate('DisciplineB', 'research_disciplines');

        // Create services and attach disciplines via tags
        $serviceA = Service::factory()->create();
        $serviceA->attachTag($disciplineATag);

        $serviceB = Service::factory()->create();
        $serviceB->attachTag($disciplineBTag);

        // Create accepted proposals and associate them with services
        Proposal::factory()->count(3)->create(['status' => 'accepted'])->each(function ($proposal) use ($serviceA) {
            $proposal->services()->attach($serviceA);
        });
        Proposal::factory()->count(2)->create(['status' => 'accepted'])->each(function ($proposal) use ($serviceB) {
            $proposal->services()->attach($serviceB);
        });


        $this->assertEquals(5, Proposal::count());

        $acceptedByDiscipline = $this->statisticsService->getAcceptedProposalsByDiscipline();

        $this->assertCount(2, $acceptedByDiscipline);

        $filteredDisciplineA = $acceptedByDiscipline->filter(function ($item) {
            $domain = json_decode($item->research_disciplines, true); // Convert JSON string to an array
            return isset($domain['en']) && $domain['en'] === 'DisciplineA';
        });

        $filteredDisciplineB = $acceptedByDiscipline->filter(function ($item) {
            $domain = json_decode($item->research_disciplines, true); // Convert JSON string to an array
            return isset($domain['en']) && $domain['en'] === 'DisciplineB';
        });

        $this->assertEquals(3, $filteredDisciplineA->first()->total);
        $this->assertEquals(2, $filteredDisciplineB->first()->total);
    }

    public function testGetTotalUsers()
    {
        User::factory()->count(15)->create();
        $totalUsers = $this->statisticsService->getTotalUsers();
        $this->assertEquals(15, $totalUsers);
    }

    public function testGetUsersPerProposal()
    {
        Proposal::factory()->count(5)->create()->each(function ($proposal) {
            $proposal->applicants()->attach(User::factory()->count(1)->create(), ['leader' => true, 'alias' => false]);
            $proposal->applicants()->attach(User::factory()->count(1)->create(), ['leader' => false, 'alias' => true]);
            $proposal->applicants()->attach(User::factory()->count(1)->create(), ['leader' => false, 'alias' => false]);
        });

        $usersPerProposal = $this->statisticsService->getUsersPerProposal();

        $this->assertCount(5, $usersPerProposal);
        foreach ($usersPerProposal as $proposal) {
            $this->assertEquals(3, $proposal->applicants->count());
        }
    }

    // Add more tests for other methods as needed...
}
