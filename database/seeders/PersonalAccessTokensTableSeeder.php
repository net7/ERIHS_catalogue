<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PersonalAccessTokensTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('personal_access_tokens')->delete();
        
        \DB::table('personal_access_tokens')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 5,
                'name' => 'auth_token',
                'token' => '6e9d963b7b1201c6520c9047328a433e3c158a5a4a442ea63dd949141bf2bcf8',
                'abilities' => '["*"]',
                'last_used_at' => NULL,
                'expires_at' => NULL,
            ),
        ));
        
        
    }
}