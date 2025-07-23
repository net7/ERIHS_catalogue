<?php

namespace Tests\Unit;

use App\Enums\ProposalStatus;
use App\Commands\ProcessCallsClosure;
use App\Models\Call;
use App\Models\Proposal;
use App\Models\User;
use App\Services\CallService;
use App\Services\ERIHSMailService;
use Carbon\Carbon;
use Database\Factories\ProposalFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;



class CallTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->refreshDatabase();
    }


    public function testThereAreNoOpenCalls()
    {
        $call = Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::yesterday()])->create();
        $this->assertNull(CallService::getOpenCalls());
        // $call->delete();
    }

    public function testOverlap(): void
    {
        $call1 = Call::factory(['start_date' => '2024-05-01', 'end_date' => '2024-07-01'])->create();




        // $this->assertDatabaseCount(Call::class, 1);

        $call = Call::factory(['start_date' => '2024-05-01', 'end_date' => '2024-07-01'])->make();
        $this->assertTrue(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call = Call::factory(['start_date' => '2024-05-01', 'end_date' => '2024-05-01'])->make();
        $this->assertTrue(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call = Call::factory(['start_date' => '2024-02-01', 'end_date' => '2024-05-01'])->make();
        $this->assertTrue(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call = Call::factory(['start_date' => '2024-03-01', 'end_date' => '2024-08-01'])->make();
        $this->assertTrue(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call = Call::factory(['start_date' => '2024-03-01', 'end_date' => '2024-06-01'])->make();
        $this->assertTrue(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call = Call::factory(['start_date' => '2024-06-01', 'end_date' => '2024-08-01'])->make();
        $this->assertTrue(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call = Call::factory(['start_date' => '2024-02-01', 'end_date' => '2024-04-30'])->make();
        $this->assertFalse(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call = Call::factory(['start_date' => '2024-07-02', 'end_date' => '2024-07-30'])->make();
        $this->assertFalse(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call2 = Call::factory(['start_date' => '2024-07-18', 'end_date' => '2024-09-19'])->create();

        // $this->assertDatabaseCount(Call::class, 2);

        $call1->delete();

        // $this->assertDatabaseCount(Call::class, 1);

        $call = Call::factory(['start_date' => '2024-07-19', 'end_date' => '2024-07-24'])->make();
        $this->assertTrue(CallService::checkIfDatesOverlap($call->start_date, $call->end_date));

        $call2->delete();

        // $this->assertDatabaseCount(Call::class, 0);

    }


    public function testThereAreOpenCalls()
    {

        Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::today()->add(2, 'month')])->create();
        $this->assertNotNull(CallService::getOpenCalls());
    }

    public function testCallIsOpen()
    {
        $call = Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::today()])->make();

        $this->assertTrue($call->isOpen());

        $this->travel(-1)->days();
        $this->assertTrue($call->isOpen());
        $this->assertFalse($call->isClosed());

        $this->travelBack();

        $this->travel(-2)->days();
        $this->assertFalse($call->isOpen());
        $this->assertFalse($call->isClosed());

        $this->travelBack();

        $this->travel(1)->days();
        $this->assertFalse($call->isOpen());
        $this->assertTrue($call->isClosed());
    }

    public function testClosingProcedure()
    {

        $this->mock(ERIHSMailService::class, function (MockInterface $mock) {
            $mock->expects('applicationFeasible')->times(3);
            $mock->expects('notifyNewUser')->times(1);
        });

        $call = Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::today()])->create();

        $call->refresh();

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertFalse($call->closing_procedures_carried_out);
        $this->assertFalse($call->isClosed());
        $this->assertFalse($call->isProcessed());
        $this->assertFalse($call->isClosedAndProcessed());

        $proposals = Proposal::factory(['call_id' => $call->id])->count(3)->create();

        $this->assertEquals(3, $call->proposals->count());

        foreach (Proposal::all() as $proposal) {
            $this->assertEquals($call->id, $proposal->call->id);
        }

        foreach ($call->proposals as $proposal) {
            $this->assertTrue($proposals->contains($proposal));
            $proposal->update(['status' => ProposalStatus::SUBMITTED->value]);
            $proposal->makeTransitionAndSave(ProposalStatus::FEASIBLE->value);
        }

        $this->travel(1)->days();


        $this->assertEquals(1, CallService::getClosedCalls()->count());
        // (new ProcessCallsClosure())->handle();

        $this->artisan('app:process-calls-closure')->assertExitCode(0);

        $call->refresh();

        $this->assertTrue($call->closing_procedures_carried_out);
        $this->assertTrue($call->isClosed());
        $this->assertTrue($call->isProcessed());
        $this->assertTrue($call->isClosedAndProcessed());
    }

    public function testDatesDontOverlapWhenModifyingCall()
    {
        $call = Call::factory(['start_date' => Carbon::yesterday(), 'end_date' => Carbon::today()])->create();

        $call->update(['start_date' => Carbon::yesterday()->add(1, 'day')]);

        $this->assertFalse(CallService::checkIfDatesOverlap($call->start_date, $call->end_date, $call->id));
    }
}
