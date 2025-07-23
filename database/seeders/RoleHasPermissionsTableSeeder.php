<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleHasPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('role_has_permissions')->delete();
        
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 6,
                'role_id' => 1,
            ),
            1 => 
            array (
                'permission_id' => 7,
                'role_id' => 1,
            ),
            2 => 
            array (
                'permission_id' => 1,
                'role_id' => 2,
            ),
            3 => 
            array (
                'permission_id' => 2,
                'role_id' => 2,
            ),
            4 => 
            array (
                'permission_id' => 1,
                'role_id' => 4,
            ),
            5 => 
            array (
                'permission_id' => 8,
                'role_id' => 4,
            ),
            6 => 
            array (
                'permission_id' => 9,
                'role_id' => 4,
            ),
            7 => 
            array (
                'permission_id' => 10,
                'role_id' => 4,
            ),
            8 => 
            array (
                'permission_id' => 11,
                'role_id' => 4,
            ),
            9 => 
            array (
                'permission_id' => 12,
                'role_id' => 4,
            ),
            10 => 
            array (
                'permission_id' => 13,
                'role_id' => 4,
            ),
            11 => 
            array (
                'permission_id' => 14,
                'role_id' => 4,
            ),
            12 => 
            array (
                'permission_id' => 16,
                'role_id' => 4,
            ),
            13 => 
            array (
                'permission_id' => 17,
                'role_id' => 4,
            ),
            14 => 
            array (
                'permission_id' => 18,
                'role_id' => 5,
            ),
            15 => 
            array (
                'permission_id' => 19,
                'role_id' => 5,
            ),
            16 => 
            array (
                'permission_id' => 20,
                'role_id' => 5,
            ),
            17 => 
            array (
                'permission_id' => 21,
                'role_id' => 5,
            ),
            18 => 
            array (
                'permission_id' => 22,
                'role_id' => 5,
            ),
            19 => 
            array (
                'permission_id' => 1,
                'role_id' => 6,
            ),
            20 => 
            array (
                'permission_id' => 2,
                'role_id' => 6,
            ),
            21 => 
            array (
                'permission_id' => 3,
                'role_id' => 6,
            ),
            22 => 
            array (
                'permission_id' => 4,
                'role_id' => 6,
            ),
            23 => 
            array (
                'permission_id' => 5,
                'role_id' => 6,
            ),
            24 => 
            array (
                'permission_id' => 6,
                'role_id' => 6,
            ),
            25 => 
            array (
                'permission_id' => 9,
                'role_id' => 6,
            ),
            26 => 
            array (
                'permission_id' => 10,
                'role_id' => 6,
            ),
            27 => 
            array (
                'permission_id' => 11,
                'role_id' => 6,
            ),
            28 => 
            array (
                'permission_id' => 12,
                'role_id' => 6,
            ),
            29 => 
            array (
                'permission_id' => 13,
                'role_id' => 6,
            ),
            30 => 
            array (
                'permission_id' => 14,
                'role_id' => 6,
            ),
            31 => 
            array (
                'permission_id' => 16,
                'role_id' => 6,
            ),
            32 => 
            array (
                'permission_id' => 17,
                'role_id' => 6,
            ),
        ));
        
        
    }
}