<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProposalServiceTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        // \DB::table('proposal_service')->delete();

        \DB::table('proposal_service')->insert(array (
            0 =>
            array (
                'id' => 101,
                'proposal_id' => 101,
                'service_id' => 4,
                // 'first_choice_start_date' => '2025-01-24',
                // 'first_choice_end_date' => '2025-01-24',
                // 'second_choice_start_date' => '2024-10-12',
                // 'second_choice_end_date' => '2024-10-12',
                'notes' => 'da marzo a giugno',
                'number_of_days' => 2,
                'feasible' => 'to_be_defined',
                'motivation' => NULL,
                'access' => NULL,
            ),
            1 =>
            array (
                'id' => 102,
                'proposal_id' => 101,
                'service_id' => 3,
                // 'first_choice_start_date' => '2024-08-23',
                // 'first_choice_end_date' => '2024-08-23',
                // 'second_choice_start_date' => '2024-08-23',
                // 'second_choice_end_date' => '2024-08-25',
                'notes' => 'da aprile a maggio',
                'number_of_days' => 3,
                'feasible' => 'to_be_defined',
                'motivation' => NULL,
                'access' => NULL,
            ),
            2 =>
            array (
                'id' => 103,
                'proposal_id' => 101,
                'service_id' => 7,
                // 'first_choice_start_date' => '2024-11-22',
                // 'first_choice_end_date' => '2024-11-27',
                // 'second_choice_start_date' => '2025-02-26',
                // 'second_choice_end_date' => '2025-03-06',
                'notes' => 'da luglio a settembre',
                'number_of_days' => 3,
                'feasible' => 'to_be_defined',
                'motivation' => NULL,
                'access' => NULL,
            ),
        ));


    }
}
