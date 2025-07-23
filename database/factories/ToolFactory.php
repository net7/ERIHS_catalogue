<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tool>
 */
class ToolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {


        return [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
            'potential_results' => $this->faker->paragraph(),
            'last_checked_date' => $this->faker->date(),
            'organization_id' => Organization::inRandomOrder()->first()->id,

        ];
    }
}
