<?php

namespace Database\Factories;

use App\Enums\MolabOwnershipConsent;
use App\Enums\ProposalStatus;
use App\Enums\ProposalType;
use App\Models\Call;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Tags\Tag;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Proposal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $molabObjectMaterialTags = Tag::getWithType('material');


        $type = ProposalType::names()[array_rand(ProposalType::names())];
        $continuation = null;
        if ($type == ProposalType::LONG_TERM_PROJECT->name) {
            $continuation = $this->faker->paragraph();
        }
        $resubmission = null;
        if ($type == ProposalType::RESUBMISSION->name) {
            $resubmission = $this->faker->word();
        }
        $numberOfApplicants = rand(1, 5);

        $call = Call::first();
        if (!$call) {
            $call = Call::factory()->create();
        }

        return [
            'name' => $this->faker->realText(100),
            'acronym' => $this->faker->word(),
            'type' => $type,
            'continuation_motivation' => $continuation,
            'resubmission_previous_proposal_number' => $resubmission,

            'cv' => implode(' ', $this->faker->paragraphs(2)),

            'providers_contacted' => $this->faker->boolean(),
            'facility_contacted' => $this->faker->boolean(),
            'comment' => $this->faker->realText(100),

            'scientific_background' => $this->faker->paragraph(),
            'had_second_draft' => $this->faker->boolean(),


            'status' => collect(ProposalStatus::values())->random(),

            'archlab_type' => $this->faker->word(),
            'archlab_type_other' => $this->faker->word(),
            'molab_quantity' => $this->faker->numberBetween(1, 400),
            'molab_objects_data' => [
                [
                    'molab_object_type' => $this->faker->randomElements([
                        'Artwork(s)',
                        'Monument(s)',
                        'Sample(s)',
                        'Archaeological site(s)',
                    ], 2),
                    // TODO: attach ? deal with it better
                    'molab_object_material' => $this->faker->randomElement($molabObjectMaterialTags),
                    'molab_object_size' => $this->faker->paragraph(),
                    'molab_object_location' => $this->faker->paragraph(),
                    'molab_object_ownership' => $this->faker->paragraph(),
                    'molab_object_ownership_consent' => $this->faker->randomElement(MolabOwnershipConsent::options()),
                    'molab_object_ownership_comment' => $this->faker->paragraph(),
                    //'molab_object_ownership_consent_file' => $this->faker->file() // TODO: complete and use?
                    'molab_object_note' => $this->faker->paragraph(),
                ]],
            'molab_drone_flight' => $this->faker->randomElement([
                'requested',
                'received',
                'other',
                'non_applicable'
            ]),
            // 'molab_drone_flight_file' => $this->faker->file() // TODO: complete and use?
            'molab_drone_flight_comment' => $this->faker->paragraph(),
            'molab_note' => $this->faker->paragraph(),
            'molab_logistic' => $this->faker->paragraph(),
            'molab_x_ray' => $this->faker->boolean(),
            // 'molab_x_ray_file'  => $this->faker->file() // TODO: complete and use?


            'fixlab_quantity' => $this->faker->numberBetween(1, 400),
            'fixlab_objects_data' => [[
                'fixlab_object_type' => $this->faker->randomElements([
                    'Object',
                    'Sample',
                    'Monument(s)',
                ], 2),
                'fixlab_object_material' => $this->faker->randomElement($molabObjectMaterialTags),
                'fixlab_number_of_measures' => $this->faker->numberBetween(1, 20),
                'fixlab_object_form' => $this->faker->paragraph(),
                'fixlab_object_size' => $this->faker->paragraph(),
                'fixlab_object_temperature' => $this->faker->paragraph(),
                'fixlab_object_air_condition' => $this->faker->paragraph(),
                'fixlab_object_ownership' => $this->faker->paragraph(),
                'fixlab_object_preparation' => $this->faker->paragraph(),
                'fixlab_object_notes' => $this->faker->paragraph(),
            ]],
            'fixlab_logistic' => $this->faker->paragraph(),
            'call_id' => $call->id,

        ];
    }
}
