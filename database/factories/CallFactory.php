<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Call>
 */
class CallFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'name' => 'Call di test',
                'start_date' => Carbon::yesterday(),
                'end_date' => Carbon::now()->add(2, 'year'),
                'closing_procedures_carried_out' => false,
                'call_pdf_path' => NULL,
                'form_pdf_path' => NULL,
        ];
    }
}
