<?php

namespace Tests\Unit;

use App\Models\Call;
use App\Models\Proposal;
use App\Models\Organization;
use App\Models\Service;
use App\Models\User;
use App\Services\ERIHSMailService;
use Carbon\Carbon;
use Database\Seeders\FakeDataSeeder;
use Database\Seeders\VocabularySeeder;
use Mockery\MockInterface;
use Spatie\Tags\Tag;
use Tests\TestCase;
class ProposalTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function setUp():  void
    {
        parent::setUp();

        $this->mock(ERIHSMailService::class, function (MockInterface $mock) {
            $mock->expects('notifyNewUser')->times(7);
        });

        (new VocabularySeeder())->run();

        Organization::factory()->count(10)->create();
        FakeDataSeeder::createUsers(2, User::SERVICE_MANAGER);
        FakeDataSeeder::createServices(1);
        $service = Service::first();

        $platforms = Tag::getWithType('e-rihs_platform');
        $platform = $platforms->where('name', 'Molab');
        $service->attachTag($platform);
        $service->save();
        Call::factory()->create(
            ['start_date' => Carbon::yesterday(),'end_date' => Carbon::now()->add(2, 'days')]
        );

        User::factory()->create();
        FakeDataSeeder::createProposals(1);
    }

    public function testFeasibility(){
        $this->mock(ERIHSMailService::class, function (MockInterface $mock) {
            // $mock->expects('applicationFeasible');
            $mock->expects('notifyNewUser')->times(4);
        });

        $call = Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::today()])->create();

        $call->refresh();

        $serviceManager1 = User::factory()->create();
        $serviceManager1->assignRole('service_manager');
        $serviceManager2 = User::factory()->create();
        $serviceManager2->assignRole('service_manager');

        $service1 = Service::factory([
            'service_manager_id' => $serviceManager1->id,
            'second_service_manager_id' => $serviceManager2->id
        ]);

        $serviceManager3 = User::factory()->create();
        $serviceManager3->assignRole('service_manager');
        $serviceManager4 = User::factory()->create();
        $serviceManager4->assignRole('service_manager');

        $service2 = Service::factory([
            'service_manager_id' => $serviceManager3->id,
            'second_service_manager_id' => $serviceManager4->id
        ]);

        $proposal = Proposal::factory([
            'call_id' => $call->id,
            ''
        ]);

    }


}
