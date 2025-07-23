<?php

namespace Database\Seeders;

use App\Models\Call;
use App\Models\Method;
use App\Models\MethodServiceTool;
use App\Models\Proposal;
use App\Models\ProposalService;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Tool;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Tags\Tag;

class FakeDataSeeder extends Seeder
{


    public static function createReviewers($count = 1){
        self::createUsers($count, User::REVIEWER_ROLE);
    }
    public static function createUsers($count, $role)
    {

        $roles = Role::all()->pluck('name');
        $research_disciplines_tags = Tag::getWithType('research_disciplines');

            User::factory()->count($count)->create(['complete_profile' => 1])->each(function ($user) use ($role, $research_disciplines_tags) {

                $user->assignRole($role);
                $user->attachTag(self::getRandomNationality());
                $user->attachTag(self::getRandomPersonalTitle());
                $user->attachTag(self::getRandomHICountryCode());
                if ($role == User::REVIEWER_ROLE) {
                    $user->object_types = array_rand(\App\Services\ProposalService::getAllObjectTypes(), 3);
                    $user->attachTags(self::getRandomMaterials());
                    $user->attachTags(self::getRandomTechniques());
                    $user->number_of_reviews = rand(env('NUMBER_OF_REVIEWS_PER_YEAR'), 5);
                    foreach ($research_disciplines_tags->random(3) as $research_disciplines_tag) {
                        $user->attachTag($research_disciplines_tag);
                    }
                    $user->terms_of_service = true;
                    $user->confidentiality = true;
                }
                if ($role == User::SERVICE_MANAGER) {
                    $organization = Organization::get()->random()->id;
                    $user->organizations()->attach($organization);
                }
                $user->save();
            });
    }

    public static function getRandomTechniques($count = 3)
    {
        $techniques = Tag::getWithType('technique');
        return $techniques->random($count);
    }

    public static function getRandomMaterials($count = 3)
    {
        $materials = Tag::getWithType('material');
        return $materials->random($count);
    }

    public static function getRandomNationality()
    {
        $nationalities = Tag::getWithType('country');
        return $nationalities->random();
    }

    public static function getRandomPersonalTitle()
    {
        $personalTitles = Tag::getWithType('personal_title');
        return $personalTitles->random();
    }

    public static function getRandomHICountryCode()
    {
        $institutionCountries = Tag::getWithType('institution_country');
        return $institutionCountries->random();
    }

    public static function createServices($count = 1)
    {

        Service::factory(['service_active' => true])->count($count)->create();
        $platforms = Tag::getWithType('e-rihs_platform');
        $techniques = Tag::getWithType('technique');
        $organizationRole = Tag::getWithType('provider_role');
        $operatingLanguages = Tag::getWithType('operating_language');
        $readinessLevel = Tag::getWithType('readiness_level');
        $researchDisciplines = Tag::getWithType('research_disciplines');
        $serviceAccessPeriodUnit = Tag::getWithType('period_unit');
        $measurableProperties = Tag::getWithType('material');

        $services = [];

        foreach (Service::all() as $service) {

            $tool = Tool::factory()->create();
            $method = Method::factory()->create();

            $mst = new MethodServiceTool();
            $mst->service_id = $service->id;
            $mst->tool_id = $tool->id;
            $mst->method_id = $method->id;
            $mst->save();

            $platform = $platforms->random();
            $service->attachTag($platform);
            if ($service->getPlatform() == 'Digilab') {
                $service->application_required = false;
                $service->url = 'https://example.com/';
            } else {
                $service->application_required = true;
            }
            $service->attachTag($techniques->random());
            $service->attachTag($organizationRole->random());
            $service->attachTag($operatingLanguages->random());
            $service->attachTag($readinessLevel->random());
            $service->attachTag($researchDisciplines->random());
            $service->attachTag($serviceAccessPeriodUnit->random());
            $service->attachTag($measurableProperties->random());

            $service->save();
            $services [] = $service;
        }

        Service::factory(['service_active' => false])->count($count)->create();

        return $services;

    }

    public static function createProposals($count = 100)
    {

        $adminUser = User::first();
        Auth::login($adminUser);
        $proposals = Proposal::factory()->count($count)->create();
        foreach ($proposals as $proposal) {
            $proposalService = new ProposalService();
            $proposalService->service_id = Service::withAnyTags(['Molab', 'Archlab', 'Fixlab'], 'e-rihs_platform')->get()->random()->id;
            $proposalService->proposal_id = $proposal->id;
            $proposalService->save();
        }

        Proposal::all()->each(function ($proposal) {
            $users = User::all()->random(1, 5)->pluck('id')->toArray();
            $proposal->applicants()->syncWithPivotValues($users, ['leader' => random_int(0, 1)]);
        });

    }

    public static function createOrganizations($count = 10)
    {
        Organization::factory()->count($count)->create();

        $countries = Tag::getWithType('country');

        foreach (Organization::all() as $organization) {
            $organization->attachTag($countries->random());
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        self::createOrganizations(10);

        $this->createReviewers(10);

        $this->createServices(10);
        $this->createServices(10);
        $this->createServices(10);
        $this->createServices(10);
        $this->createServices(10);

        Call::factory()->create(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::now()->add(2, 'month')]);

        // $this->createProposals(100);

        // $proposal = Proposal::find(1);
        // $proposal->type = 'NEW';
        // $proposal->resubmission_previous_proposal_number = null;
        // $proposal->applicants()->syncWithPivotValues(User::find(1), ['leader' => 1]);
        // $proposal->status = ProposalStatus::DRAFT;
        // $proposal->save();
        // $services = $proposal->services;
        // $organization_id = $services[0]->organization_id;
        // $organizationUser = new OrganizationUser();
        // $organizationUser->user_id = 1;
        // $organizationUser->organization_id = $organization_id;
        // $organizationUser->save();
        // $cart = new Cart();
        // $cart->user_id = 1;
        // $cart->cart_name = "cart";
        // $serviceIds = $proposal->services()->pluck('service_id');
        // $cart->data = json_encode($serviceIds);
        // $cart->save();

        $this->call(ProposalsTableSeeder::class);
        $this->call(ProposalServiceTableSeeder::class);
        $this->call(ApplicantProposalTableSeeder::class);

    }
}
