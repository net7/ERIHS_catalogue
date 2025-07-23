<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('users')->delete();

        \DB::table('users')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'admin',
                'surname' => 'admin',
                'city' => 'Pisa',
                'country' => 'country',
                'email' => env('ADMIN_USER_EMAIL'),
                'email_verified_at' => '2023-06-16 09:46:28',
                'password' => Hash::make(env('ADMIN_USER_PASSWORD')),
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'two_factor_confirmed_at' => NULL,
                'remember_token' => NULL,
                'current_team_id' => NULL,
                'current_connected_account_id' => NULL,
                'profile_photo_path' => NULL,
                'terms_of_service' => 0,
                'confidentiality' => 0,
                'disciplines' => NULL,
                'number_of_reviews' => 0,
                'birth_year' => '1989',
                'gender' => 'MALE',
                'home_institution' => NULL,
                'institution_address' => ' Institution address* ',
                'institution_city' => ' Institution city* ',
                'institution_status_code' => 'UNIVERSITY',
                'job' => ' Function / Job / Title* ',
                'academic_background' => ' Academic Background* ',
                'position' => 'UNDERGRADUATE',
                'office_phone' => NULL,
                'mobile_phone' => NULL,
                'short_cv' => ' Short Curriculum Vitae* ',
                'complete_profile' => 0,
                'first_login' => 0,
                'api_token' => NULL,
            ),
        ));
    }
}
