<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CallsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('calls')->delete();
        
        \DB::table('calls')->insert(array (
            0 => 
            array (
                'id' => 1,
                'created_at' => '2024-06-26 08:25:01',
                'updated_at' => '2024-06-26 08:46:09',
                'name' => 'Call di test',
                'start_date' => '2024-01-01',
                'end_date' => '2033-02-09',
                'call_pdf_path' => NULL,
                'form_pdf_path' => NULL,
            ),
        ));
        
        
    }
}