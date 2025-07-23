<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProposalsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        // \DB::table('proposals')->delete();

        \DB::table('proposals')->insert(array (
            0 =>
            array (
                'id' => 101,
                'uuid' => '0ac0d4be-3f7c-425d-b036-4b8bf24cba87',
                'published_at' => NULL,
                'is_published' => 0,
                'is_current' => 1,
                'publisher_type' => 'App\\Models\\User',
                'publisher_id' => 6,
                'name' => 'titotletto',
                'acronym' => 'tt2',
                'type' => 'NEW',
                'continuation_motivation' => NULL,
                'resubmission_previous_proposal_number' => NULL,
                'related_project' => NULL,
                'cv' => NULL,
                'providers_contacted' => 1,
                'facility_contacted' => 1,
                'internal_status' => NULL,
                'status' => 'draft',
                'comment' => NULL,
                'scope_note' => NULL,
                'project_justification' => NULL,
                'scientific_background' => NULL,
                'erihs_id' => NULL,
                'eligibility' => NULL,
                'whom' => 'Nico',
                'had_second_draft' => 0,
                'archlab_type' => 'Reports',
                'archlab_type_other' => NULL,
                'molab_quantity' => 2,
            'molab_objects_data' => '[{"molab_object_size": "22", "molab_object_type": ["Monument(s)"], "molab_object_location": "ED", "molab_object_material": ["5551", "5554"], "molab_object_ownership": "KLJ", "molab_object_ownership_comment": null, "molab_object_ownership_consent": "requested", "molab_object_ownership_consent_file": "01J4K61EAPXXN4Y8PFK98RG524.pdf"}, {"molab_object_size": "23", "molab_object_type": ["Artwork(s)"], "molab_object_location": "eeee", "molab_object_material": ["5553", "5555"], "molab_object_ownership": "mmmmiiiooo", "molab_object_ownership_comment": null, "molab_object_ownership_consent": "received", "molab_object_ownership_consent_file": "01J4NSGMBTQW14Q9VFHMQGR80M.pdf"}]',
                'molab_drone_flight' => 'received',
                'molab_drone_flight_file' => '01J4K61EASK12RJBFDMWZY6MNF.pdf',
                'molab_drone_flight_comment' => NULL,
                'molab_note' => NULL,
                'molab_logistic' => 'dfasdfsd',
                'molab_x_ray' => 1,
                'molab_x_ray_file' => '01J4K61EAVHVYYD89SXE7HTNW8.pdf',
                'fixlab_quantity' => 2,
            'fixlab_objects_data' => '[{"fixlab_object_form": "SQUARE", "fixlab_object_size": "23", "fixlab_object_type": ["Object"], "fixlab_object_notes": "kkk", "fixlab_object_material": ["5553", "5555"], "fixlab_object_ownership": "LIIIL", "fixlab_number_of_measures": "2", "fixlab_object_preparation": "KKKKk", "fixlab_object_temperature": "25", "fixlab_object_air_condition": "16"}, {"fixlab_object_form": "squ", "fixlab_object_size": "99x33", "fixlab_object_type": ["Sample", "Monument(s)"], "fixlab_object_notes": "hkjlh", "fixlab_object_material": ["1035"], "fixlab_object_ownership": "iirrrll", "fixlab_number_of_measures": "41", "fixlab_object_preparation": "djjdj", "fixlab_object_temperature": "11", "fixlab_object_air_condition": "88"}, {"fixlab_object_form": "223ddd", "fixlab_object_size": "lll", "fixlab_object_type": ["Monument(s)"], "fixlab_object_notes": "fsg", "fixlab_object_material": ["5331", "5554"], "fixlab_object_ownership": "fgsklfgj", "fixlab_number_of_measures": "44", "fixlab_object_preparation": "lkjlkj", "fixlab_object_temperature": "lldll", "fixlab_object_air_condition": "ldllerl"}]',
                'fixlab_logistic' => ',k,kh',
                'call_id' => 1,
                'status_history' => '[{"timestamp":"2024-08-06 06:29:18","status_code":"draft","info":[],"position":0}]',
                'activities' => '[]',
            ),
        ));


    }
}
