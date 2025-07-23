<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array (
            0 =>
            array (
                'id' => 4,
                'name' => 'admin',
                'guard_name' => 'web',
            ),
            // 1 =>
            // array (
            //     'id' => 3,
            //     'name' => 'coordinator',
            //     'guard_name' => 'web',
            // ),
            2 =>
            array (
                'id' => 6,
                'name' => 'help desk',
                'guard_name' => 'web',
            ),
            3 =>
            array (
                'id' => 5,
                'name' => 'service_manager',
                'guard_name' => 'web',
            ),
            4 =>
            array (
                'id' => 2,
                'name' => 'reviewer',
                'guard_name' => 'web',
            ),
            5 =>
            array (
                'id' => 1,
                'name' => 'user',
                'guard_name' => 'web',
            ),
        ));


    }
}
