<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Enums\ProposalType;
use App\Models\Call;
use App\Models\Organization;
use App\Models\Service;
use App\Models\User;
use App\Services\ERIHSCartService;
use App\Services\ERIHSLocalCartService;
use Database\Seeders\FakeDataSeeder;
use Database\Seeders\MailTemplatesTableSeeder;
use Database\Seeders\VocabularySeeder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;
use SebastianBergmann\Type\VoidType;
use Spatie\Tags\Tag;
use Tests\TestCase;
use App\Services\ERIHSMailService;
use Carbon\Carbon;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Enums\ProposalReviewerStatus;
use Livewire\Livewire;
use App\Filament\Resources\ProposalResource;

class ProposalTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        (new VocabularySeeder())->run();

        (new MailTemplatesTableSeeder())->run();

        Organization::factory()->count(10)->create();
        FakeDataSeeder::createUsers(2, User::SERVICE_MANAGER);
        FakeDataSeeder::createServices(1);
        $service = Service::first();



        $platforms = Tag::getWithType('e-rihs_platform');
        $platform = $platforms->where('name', 'Molab');
        $service->attachTag($platform);
        $service->save();
    }

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/catalogue');
        $response->assertStatus(302);
    }

    public function testCannotAccessProposalFormWithoutOpenCalls(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        ERIHSCartService::addItem(Service::first());

        $response = $this->actingAs($user)->get('proposal');
        $response->assertRedirect('/dashboard');
    }

    public function testCannotAccessProposalFormWithoutCompleteProfile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        ERIHSCartService::addItem(Service::first());
        Call::factory()->create();

        $response = $this->actingAs($user)->get('proposal');
        $response->assertRedirect(route('wizard'));
    }

    public function testCannotAccessProposalFormWithoutServicesInCart(): void
    {
        $user = User::factory(['complete_profile' => true])->create();
        $this->actingAs($user);
        Call::factory()->create();

        $response = $this->actingAs($user)->get('proposal');
        $response->assertRedirect(route('catalogue'));
    }

    public function testCanAccessProposalForm(): void
    {

        $user = User::factory(['complete_profile' => true])->create();
        $this->actingAs($user);
        ERIHSCartService::addItem(Service::first());
        Call::factory()->create();

        $response = $this->actingAs($user)->get('proposal');
        $response->assertStatus(200);
    }

    /*
    public function testCanSubmitProposal(): void
    {

        $undoRepeaterFake = Repeater::fake();
        FileUpload::configureUsing(function(FileUpload $component){
            $component->preserveFilenames();
        });

        $mock = $this->mock(ERIHSLocalCartService::class, function (MockInterface $mock) {
            $mock->shouldReceive('emptyCart');
        });

        // $user = User::factory(['complete_profile' => true])->create();
        // $this->actingAs($user);
        // $platforms = Tag::getWithType('e-rihs_platform');
        // $molab = $platforms->where('name', 'Molab');
        // $archlab = $platforms->where('name', 'Archlab');
        // $fixlab = $platforms->where('name', 'Fixlab');

        // $services = FakeDataSeeder::createServices(6);
        // $molabService1 = $services[0];
        // $molabService1->attachTag($molab);
        // $molabService1->save();

        // $molabService2 = $services[1];
        // $molabService2->attachTag($molab);
        // $molabService2->save();

        // $archlabService1 = $services[2];
        // $archlabService1->attachTag($archlab);
        // $archlabService1->save();

        // $archlabService2 = $services[3];
        // $archlabService2->attachTag($archlab);
        // $archlabService2->save();

        // $fixlabService1 = $services[4];
        // $fixlabService1->attachTag($fixlab);
        // $fixlabService1->save();

        // $fixlabService2 = $services[5];
        // $fixlabService2->attachTag($fixlab);
        // $fixlabService2->save();

        // $services = [
        //     $molabService1,
        //     $molabService2,
        //     $archlabService1,
        //     $archlabService2,
        //     $fixlabService1,
        //     $fixlabService2
        // ];

        // foreach ($services as $service){
        //     ERIHSCartService::addItem($service);
        // }

        // Call::factory()->create();

        // $response = $this->actingAs($user)->get('proposal');
        // $response->assertStatus(200);

        $services = $this->prepareDataForFormFilling();

        $file1 =  UploadedFile::fake()->create($this->faker->word().'.jpg');
        $file2 =  UploadedFile::fake()->create($this->faker->word().'.jpg');
        $file3 =  UploadedFile::fake()->create($this->faker->word().'.jpg');
        $file4 =  UploadedFile::fake()->create($this->faker->word().'.jpg');

        $livewire = Livewire::test(\App\Livewire\CreateProposal::class)
        ->fillForm(
                    $this->proposalFormDataArray($services, $file1, $file2, $file3, $file4),
                    'proposalForm'
            )
            ;

        $livewire->call('submit');

        $livewire->assertHasNoFormErrors(formName: 'proposalForm');


        $undoRepeaterFake();
    }

    */

    private function prepareDataForFormFilling()
    {

        $user = User::factory(['complete_profile' => true])->create();
        $this->actingAs($user);
        $platforms = Tag::getWithType('e-rihs_platform');
        $molab = $platforms->where('name', 'Molab');
        $archlab = $platforms->where('name', 'Archlab');
        $fixlab = $platforms->where('name', 'Fixlab');

        $services = FakeDataSeeder::createServices(6);
        $molabService1 = $services[0];
        $molabService1->attachTag($molab);
        $molabService1->save();

        $molabService2 = $services[1];
        $molabService2->attachTag($molab);
        $molabService2->save();

        $archlabService1 = $services[2];
        $archlabService1->attachTag($archlab);
        $archlabService1->save();

        $archlabService2 = $services[3];
        $archlabService2->attachTag($archlab);
        $archlabService2->save();

        $fixlabService1 = $services[4];
        $fixlabService1->attachTag($fixlab);
        $fixlabService1->save();

        $fixlabService2 = $services[5];
        $fixlabService2->attachTag($fixlab);
        $fixlabService2->save();

        $services = [
            $molabService1,
            $molabService2,
            $archlabService1,
            $archlabService2,
            $fixlabService1,
            $fixlabService2
        ];

        foreach ($services as $service) {
            ERIHSCartService::addItem($service);
        }

        Call::factory()->create();

        $response = $this->actingAs($user)->get('proposal');
        $response->assertStatus(200);

        return $services;
    }


    private function proposalFormDataArray($services, $file1, $file2, $file3, $file4)
    {

        $documentTypes = collect(["Reports", "Cross-sections", "Analytical data", "Images", "Databases", "Photographs", "Other"]);
        $proposalServices = [];




        foreach ($services as $service) {
            $proposalServices[] = [
                'service_id' => $service->id,
                'number_of_days' => $this->faker()->randomDigitNot(0),
                'first_choice_start_date' => $this->faker()->dateTimeBetween('now', '+ 1 week')->format('Y-m-d'),
                'first_choice_end_date' => $this->faker()->dateTimeBetween('+ 1 week', '+ 2 week')->format('Y-m-d'),
                'second_choice_start_date' => $this->faker()->dateTimeBetween('now', '+ 1 week')->format('Y-m-d'),
                'second_choice_end_date' => $this->faker()->dateTimeBetween('+ 1 week', '+ 2 week')->format('Y-m-d'),
            ];
        }
        $data = [

            'archlab_type' => $documentTypes->random(),
            'archlab_type_other' => 'foo',

            'molab_quantity' => 2,

            'molab_objects_data' => [
                [
                    'molab_object_type' => 'Artwork(s)',
                    'molab_object_material' => [Tag::withType('material')->get()->random()->id],
                    'molab_object_size' => '12x23cm',
                    'molab_object_location' => 'somewhere',
                    'molab_object_ownership' => 'Mr. Owner',
                    // for some reasons if we put received here, the test enters some loop and it breaks
                    // 'molab_object_ownership_consent' => 'received',
                    'molab_object_ownership_consent' => 'other',
                    'molab_object_ownership_comment' => 'Short Comment',
                    'molab_object_ownership_consent_file' => $file1,
                    'molab_object_note' => 'Some notes'
                ],
                [
                    'molab_object_type' => 'Monument(s)',
                    'molab_object_material' => [Tag::withType('material')->get()->random()->id],
                    'molab_object_size' => '23x34cm',
                    'molab_object_location' => 'somewhere else',
                    'molab_object_ownership' => 'Mr. Loaner',
                    // for some reasons if we put received here, the test enters some loop and it breaks
                    // 'molab_object_ownership_consent' => 'received',
                    'molab_object_ownership_consent' => 'other',
                    'molab_object_ownership_comment' => 'Comment for ownership',
                    'molab_object_ownership_consent_file' => $file2,
                    'molab_object_note' => 'Some notes',
                ],
            ],
            'molab_drone_flight' => 'received',
            'molab_drone_flight_file' => $file3,
            'molab_drone_flight_comment' => 'drone flight comment',
            'molab_note' => 'molab notes',
            'molab_logistic' => 'pick them up by hand',
            'molab_x_ray' => true,
            'molab_x_ray_file' => $file4,

            'fixlab_quantity' => 2,


            'fixlab_objects_data' => [
                [
                    'fixlab_object_type' => 'Object',
                    'fixlab_object_material' => [Tag::withType('material')->get()->random()->id],
                    'fixlab_number_of_measures' => 1,
                    'fixlab_object_form' => 'the form of the object',
                    'fixlab_object_size' => '22x42cm',
                    'fixlab_object_temperature' => '20C',
                    'fixlab_object_air_condition' => '16C',
                    'fixlab_object_ownership' => 'Mr. Ow. Ner',
                    'fixlab_object_preparation' => 'Pack them tidy',
                    'fixlab_object_notes' => $this->faker->paragraph(),
                ],
                [
                    'fixlab_object_type' => 'Sample',
                    'fixlab_object_material' => [Tag::withType('material')->get()->random()->id],
                    'fixlab_number_of_measures' => 1,
                    'fixlab_object_form' => 'the form of the sample',
                    'fixlab_object_size' => '11x12cm',
                    'fixlab_object_temperature' => '32C',
                    'fixlab_object_air_condition' => '25C',
                    'fixlab_object_ownership' => 'Mr. Lo. Aner',
                    'fixlab_object_preparation' => 'Pack them tidy as well',
                    'fixlab_object_notes' => $this->faker->paragraph(),
                ],
            ],
            'fixlab_logistic' => 'Bring your truck',


            // 'name' => $this->faker->words(3),
            'name' => ' test senza faker',
            'acronym' => $this->faker->word,
            'type' => ProposalType::NEW->name,
            'resubmission_previous_proposal_number' => null,
            'related_project' => null,
            'comment' => $this->faker->sentence(10),
            'continuation_motivation' => null,

            'research_disciplines' => [Tag::withType('research_disciplines')->get()->random()->id],
            'providers_contacted' => true,
            'facility_contacted' => true,
            'whom' =>  $this->faker->name(),
            'cv' => $this->faker->paragraph(),
            'partnerProposals' => [],
            'community' => 'Social Science and Humanities',
            'proposalServices' => $proposalServices,

            // TODO: understand why it's needed
            'status' => ProposalStatus::DRAFT->value,
        ];


        return $data;
    }


    public function testThirdReviewerCannotReviewProposalAfterRankingFromUnderReview()
    {

        $this->mock(ERIHSMailService::class, function (MockInterface $mock) {
            $mock->shouldReceive('applicationInMainList')->times(1);
            $mock->shouldReceive('notifyNewUser')->times(4);
        });

        $call = Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::today()])->create();

        $call->refresh();

        $proposal = Proposal::factory([
            'call_id' => $call->id,
            'status' => ProposalStatus::UNDER_REVIEW->value,
        ])->create();

        $reviewer1 = User::factory()->create(['name' => 'reviewer1']);
        $reviewer1->assignRole(User::REVIEWER_ROLE);
        $reviewer2 = User::factory()->create(['name' => 'reviewer2']);
        $reviewer2->assignRole(User::REVIEWER_ROLE);
        $reviewer3 = User::factory()->create(['name' => 'reviewer3']);
        $reviewer3->assignRole(User::REVIEWER_ROLE);
        $helpdesk = User::factory()->create(['name' => 'helpdesk']);
        $helpdesk->assignRole(User::HELP_DESK_ROLE);


        $proposal->reviewers()->createMany([
            ['reviewer_id' => $reviewer1->id, 'status' => ProposalReviewerStatus::ACCEPTED->value],
            ['reviewer_id' => $reviewer2->id, 'status' => ProposalReviewerStatus::ACCEPTED->value],
            ['reviewer_id' => $reviewer3->id, 'status' => ProposalReviewerStatus::WAITING->value],
        ]);

        $proposalReviewer1 = ProposalReviewer::where('reviewer_id', $reviewer1->id)->where('proposal_id', $proposal->id)->first();
        $proposalReviewer2 = ProposalReviewer::where('reviewer_id', $reviewer2->id)->where('proposal_id', $proposal->id)->first();
        $proposalReviewer3 = ProposalReviewer::where('reviewer_id', $reviewer3->id)->where('proposal_id', $proposal->id)->first();


        $this->actingAs($reviewer1);
        // get the route by name
        $proposalEvaluationUrl = ProposalResource::getUrl('evaluation', ['record' => $proposal]);

        $response = $this->get($proposalEvaluationUrl);
        $response->assertStatus(200);

        $this->actingAs($helpdesk);
        $proposal->makeTransitionAndSave(ProposalStatus::RANKED_MAIN_LIST->value);

        $proposalReviewer3->refresh();
        $reviewer3->refresh();
        $proposal->refresh();

        // since the proposal has been ranked, the reviewer cannot access the evaluation page
        $this->actingAs($reviewer3);
        $response = $this->get($proposalEvaluationUrl);
        $response->assertStatus(403);

        $this->assertDatabaseHas('proposal_reviewer', [
            'reviewer_id' => $reviewer3->id,
            'status' => ProposalReviewerStatus::SKIPPED->value,
        ]);
        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => ProposalStatus::RANKED_MAIN_LIST->value,
        ]);
    }


    public function testThirdReviewerCannotReviewProposalAfterRankingFromFeasible()
    {

        $this->mock(ERIHSMailService::class, function (MockInterface $mock) {
            $mock->shouldReceive('applicationInMainList')->times(1);
            $mock->shouldReceive('notifyNewUser')->times(4);
        });

        $call = Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::today()])->create();

        $call->refresh();

        $proposal = Proposal::factory([
            'call_id' => $call->id,
            'status' => ProposalStatus::FEASIBLE->value,
        ])->create();

        $reviewer1 = User::factory()->create(['name' => 'reviewer1']);
        $reviewer1->assignRole(User::REVIEWER_ROLE);
        $reviewer2 = User::factory()->create(['name' => 'reviewer2']);
        $reviewer2->assignRole(User::REVIEWER_ROLE);
        $reviewer3 = User::factory()->create(['name' => 'reviewer3']);
        $reviewer3->assignRole(User::REVIEWER_ROLE);
        $helpdesk = User::factory()->create(['name' => 'helpdesk']);
        $helpdesk->assignRole(User::HELP_DESK_ROLE);


        $proposal->reviewers()->createMany([
            ['reviewer_id' => $reviewer1->id, 'status' => ProposalReviewerStatus::ACCEPTED->value],
            ['reviewer_id' => $reviewer2->id, 'status' => ProposalReviewerStatus::ACCEPTED->value],
            ['reviewer_id' => $reviewer3->id, 'status' => ProposalReviewerStatus::WAITING->value],
        ]);

        $proposalReviewer1 = ProposalReviewer::where('reviewer_id', $reviewer1->id)->where('proposal_id', $proposal->id)->first();
        $proposalReviewer2 = ProposalReviewer::where('reviewer_id', $reviewer2->id)->where('proposal_id', $proposal->id)->first();
        $proposalReviewer3 = ProposalReviewer::where('reviewer_id', $reviewer3->id)->where('proposal_id', $proposal->id)->first();


        $this->actingAs($reviewer1);
        // get the route by name
        $proposalEvaluationUrl = ProposalResource::getUrl('evaluation', ['record' => $proposal]);

        $response = $this->get($proposalEvaluationUrl);
        $response->assertStatus(200);

        $this->actingAs($helpdesk);
        $proposal->makeTransitionAndSave(ProposalStatus::RANKED_MAIN_LIST->value);

        $proposalReviewer3->refresh();
        $reviewer3->refresh();
        $proposal->refresh();

        // since the proposal has been ranked, the reviewer cannot access the evaluation page
        $this->actingAs($reviewer3);
        $response = $this->get($proposalEvaluationUrl);
        $response->assertStatus(403);

        $this->assertDatabaseHas('proposal_reviewer', [
            'reviewer_id' => $reviewer3->id,
            'status' => ProposalReviewerStatus::SKIPPED->value,
        ]);
        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => ProposalStatus::RANKED_MAIN_LIST->value,
        ]);
    }
}
