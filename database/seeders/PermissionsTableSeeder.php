<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('permissions')->delete();

        \DB::table('permissions')->insert(array(
            0 =>
            array(
                'id' => 2,
                'name' => 'accept proposals',
                'guard_name' => 'web',
            ),
            1 =>
            array(
                'id' => 9,
                'name' => 'administer calls',
                'guard_name' => 'web',
            ),
            2 =>
            array(
                'id' => 14,
                'name' => 'administer methods',
                'guard_name' => 'web',
            ),
            3 =>
            array(
                'id' => 10,
                'name' => 'administer proposals',
                'guard_name' => 'web',
            ),
            4 =>
            array(
                'id' => 13,
                'name' => 'administer organizations',
                'guard_name' => 'web',
            ),
            5 =>
            array(
                'id' => 16,
                'name' => 'administer services',
                'guard_name' => 'web',
            ),
            6 =>
            array(
                'id' => 8,
                'name' => 'administer site',
                'guard_name' => 'web',
            ),
            7 =>
            array(
                'id' => 17,
                'name' => 'administer tools',
                'guard_name' => 'web',
            ),
            8 =>
            array(
                'id' => 11,
                'name' => 'administer users',
                'guard_name' => 'web',
            ),
            9 =>
            array(
                'id' => 4,
                'name' => 'assign proposal to reviewers',
                'guard_name' => 'web',
            ),
            10 =>
            array(
                'id' => 12,
                'name' => 'close evaluations',
                'guard_name' => 'web',
            ),
            11 =>
            array(
                'id' => 3,
                'name' => 'comment on proposals',
                'guard_name' => 'web',
            ),
            12 =>
            array(
                'id' => 6,
                'name' => 'compile questionnaires',
                'guard_name' => 'web',
            ),
            13 =>
            array(
                'id' => 1,
                'name' => 'evaluate proposals',
                'guard_name' => 'web',
            ),
            14 =>
            array(
                'id' => 5,
                'name' => 'send memo to reviewers',
                'guard_name' => 'web',
            ),
            15 =>
            array(
                'id' => 7,
                'name' => 'submit proposals',
                'guard_name' => 'web',
            ),
            16 =>
            array(
                'id' => 18,
                'name' => 'administer own organizations',
                'guard_name' => 'web',
            ),
            17 =>
            array(
                'id' => 19,
                'name' => 'administer own services',
                'guard_name' => 'web',
            ),
            18 =>
            array(
                'id' => 20,
                'name' => 'administer own techniques',
                'guard_name' => 'web',
            ),
            19 =>
            array(
                'id' => 21,
                'name' => 'administer own tools',
                'guard_name' => 'web',
            ),
            20 =>
            array(
                'id' => 22,
                'name' => 'administer own methods',
                'guard_name' => 'web',
            ),
        ));
    }
}
