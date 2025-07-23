<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\Service;
use App\Services\ERIHSCartService;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ERIHSCartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCanAddAndRemoveItems()
    {
        User::factory()->create();
        $user = User::first();

        Organization::factory(10)->create();
        $service1 = Service::factory(['service_manager_id' => $user->id])->create();

        $this->assertEquals(1, Service::count());

        $this->actingAs($user);

        ERIHSCartService::addItem($service1->id);
        // $this->assertEquals(1, Auth::user()->id);
        $this->assertEquals(1, ERIHSCartService::getItems()->count());

        $service2 = Service::factory(['service_manager_id' => $user->id])->create();

        ERIHSCartService::addItem($service2->id);
        $this->assertEquals(2, ERIHSCartService::getItems()->count());

        ERIHSCartService::removeItem($service2->id);
        $this->assertEquals(1, ERIHSCartService::getItems()->count());

        // ERIHSCartService::emptyCart();
        // $this->assertEquals(0, ERIHSCartService::getItems()->count());


    }


}
