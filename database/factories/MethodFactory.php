<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tool>
 */
class MethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'preferred_label' => $this->faker->sentence(),
            'alternative_labels' => [
                ['alternative_codes' => $this->faker->word()]
            ],
            'method_version' => $this->faker->sentence(),
            //'creation_date'=>$this->faker->date(),
            'organization_id' => Organization::inRandomOrder()->first()->id,
            'method_type' => $this->faker->sentence(),
            'method_documentation' => $this->faker->paragraph(),
        ];
    }
}
