<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tool>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        if (Organization::count() == 0){
            Organization::factory()->count(1)->create();
        }

        User::factory()->count(2)
                ->create(['complete_profile' => 1])
                ->each(function ($user){
                    $user->assignRole(User::SERVICE_MANAGER);
                    $organization = Organization::get()->random()->id;
                    $user->organizations()->attach($organization);
                    $user->save();
                });

        // $users = new Collection();
        // // The first 4 users are created manually in the seeder, we skip those as they may miss some data
        // if (User::where('id', '>', 5)->count() >= 2){
        //     $users = User::where('id', '>', 5)->get()->random(2);
        // }

        // $serviceManagers = new Collection();
        // if ($users->count() < 2) {
        //     User::factory()->count(2)
        //         ->create(['complete_profile' => 1])
        //         ->each(function ($user) use ($serviceManagers){
        //             $user->assignRole(User::SERVICE_MANAGER);
        //             $organization = Organization::get()->random()->id;
        //             $user->organizations()->attach($organization);
        //             $user->save();
        //             $serviceManagers->add($user);
        //         });
        // } else {
        //     foreach ($users as $user) {
        //         if (!$user->hasRole(User::SERVICE_MANAGER)) {

        //             $user->assignRole(User::SERVICE_MANAGER);
        //             $organization = Organization::get()->random()->id;
        //             $user->organizations()->attach($organization);
        //             $user->save();
        //         }
        //         $serviceManagers->add($user);
        //     }
        // }

        $serviceManagers = User::role(User::SERVICE_MANAGER)->get();
        $serviceManager1 = $serviceManagers->random();
        $serviceManagers->forget($serviceManager1->id);
        $serviceManager2 = $serviceManagers->random();
        $organizations = $serviceManager1->organizations;
        if (empty($organizations)){
            $organization = Organization::get()->random()->id;
            $serviceManager1->organizations()->attach($organization);
            $serviceManager1->save();
        } else {
            $organization = $organizations->first();
        }
        if (!$organization){
            $organization = Organization::factory()->create();
        }
        $organization_id = $organization->id;

        return [
            'title' => $this->faker->sentence(),
            'summary' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'categories' => [['category' => $this->faker->word()]],
            'functions' => [['function' =>$this->faker->sentence()]],
            'slots' => $this->faker->numberBetween(3, 10),
            // 'service_manager_id' => $serviceManager1->id,
            // 'second_service_manager_id' => $serviceManager2->id,
            'application_required' => $this->faker->boolean(),
            'service_active' => $this->faker->boolean(),
            'organization_id' => $organization_id,
        ];
    }
}
