<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApplicantProposalTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        // \DB::table('applicant_proposal')->delete();
        
        \DB::table('applicant_proposal')->insert(array (
            0 => 
            array (
                'leader' => 1,
                'alias' => 0,
                'applicant_id' => 6,
                'proposal_id' => 101,
            ),
            1 => 
            array (
                'leader' => 0,
                'alias' => 1,
                'applicant_id' => 7,
                'proposal_id' => 101,
            ),
            2 => 
            array (
                'leader' => 0,
                'alias' => 0,
                'applicant_id' => 12,
                'proposal_id' => 101,
            ),
        ));
        
        
    }
}