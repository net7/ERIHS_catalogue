<?php

namespace Tests\Unit;

use App\Enums\ProposalStatus;
use App\Models\Call;
use App\Models\Proposal;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Tool;
use App\Models\User;
use App\Services\ProposalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class ProposalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Call::factory()->create(
            ['start_date' => Carbon::yesterday(),'end_date' => Carbon::tomorrow()]
        );
    }

    public function testMySubmittedProposal()
    {
        $user = User::factory()->create();
        $call = Call::first();

        $this->actingAs($user);

        $submittedProposals = ProposalService::mySubmittedProposal();
        $this->assertEmpty($submittedProposals);

        $proposal = new Proposal();
        $proposal->name = 'Proposal Name';
        $proposal->status = ProposalStatus::SUBMITTED->value;
        $proposal->call_id = $call->id;
        $proposal->save();

        ProposalService::saveLeader($proposal->id);

        $submittedProposals = ProposalService::mySubmittedProposal();

        $this->assertEquals($submittedProposals[0]->name, $proposal->name);
    }

    public function testGetProposalFormData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $call = Call::first();

        $proposal = new Proposal();
        $proposal->name = 'Proposal Name';
        $proposal->status = ProposalStatus::DRAFT;
        $proposal->call_id = $call->id;
        $proposal->save();

        $proposalFormData = ProposalService::getProposalFormData($proposal);

        $this->assertEquals($proposalFormData['name'], $proposal->name);
        $this->assertEquals($proposalFormData['status'], $proposal->status);
    }

  /*
  non piu' usato

    public function testSaveRelations()
    {

        $client = Mockery::mock(Elasticsearch\Client::class);

        // Creazione di un utente e autenticazione
        $user = User::factory()->create();
        $this->actingAs($user);

        $userApplicant1 = User::factory()->create();
        $userApplicant2 = User::factory()->create();

        // Creazione di dati di esempio per le relazioni

        $proposal = Proposal::factory()->create();
        // $tool1 = Tool::factory()->create();
        // $tool2 = Tool::factory()->create();

        $organization = Organization::factory()->create();

        $service1 = Service::factory(['organization_id' => $organization->id, 'service_manager_id'=> $user->id])->create();
        $service2 = Service::factory(['organization_id' => $organization->id, 'service_manager_id'=> $user->id])->create();


        $proposal_id = $proposal->id;
        $state = [
            'proposalServices' => [
                [
                    'service_id' => $service1->id,
                    'number_of_days' => 5,
                    'first_choice_start_date' => '2024-02-10',
                    'first_choice_end_date' => '2024-02-15',
                    'second_choice_start_date' => '2024-02-20',
                    'second_choice_end_date' => '2024-02-25',
                ],
                [
                    'service_id' => $service2->id,
                    'number_of_days' => 7,
                    'first_choice_start_date' => '2024-02-12',
                    'first_choice_end_date' => '2024-02-18',
                    'second_choice_start_date' => '2024-02-22',
                    'second_choice_end_date' => '2024-02-28',
                ],
            ],
            // 'applicantProposals' => [
            //     ['applicant_id' => $userApplicant1->id],
            //     ['applicant_id' => $userApplicant2->id],
            // ],

            // 'tags' => ['tag1', 'tag2'],

        ];


        $client = Mockery::mock(Elasticsearch\Client::class);

        // Simulazione del metodo saveRelations
        // ProposalService::saveRelations(array_keys($state), $state, $proposal_id);

        // Verifica che le relazioni tra strumenti di proposta siano state salvate correttamente nel database
        $this->assertDatabaseCount('proposal_service', 2);
        $this->assertDatabaseHas('proposal_service', [
            'proposal_id' => $proposal_id,
            'service_id' => $service1->id,
            'number_of_days' => 5,
            'first_choice_start_date' => '2024-02-10',
            'first_choice_end_date' => '2024-02-15',
            'second_choice_start_date' => '2024-02-20',
            'second_choice_end_date' => '2024-02-25',
        ]);

        $this->assertDatabaseHas('proposal_service', [
            'proposal_id' => $proposal_id,
            'service_id' => $service2->id,
            'number_of_days' => 7,
            'first_choisetIsProcessedce_start_date' => '2024-02-12',
            'first_choice_end_date' => '2024-02-18',
            'second_choice_start_date' => '2024-02-22',
            'second_choice_end_date' => '2024-02-28',
        ]);

    }
    */
}
