<?php

namespace Database\Seeders;

use Facades\Symfony\Component\String\Slugger\AsciiSlugger;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Tags\Tag;

class UpdateVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $vocabularies = config('app.vocabulary_groups');

        foreach ($vocabularies as $groupName => $group){


            echo "Importing tags with type '".$groupName."' \r\n";

            $url = env('VOCABULARY_URL') . $group;
            $response = Http::acceptJson()->get($url);

            echo "Requesting $group from $url \r\n";
            $list = $response['list'];

            foreach ($list as $id => $label){

                if ($id == null || $label == null){
                    continue;
                }
                if ($group == 'g12'){
                    Log::info("Importing $id from $group, label = $label ");
                }


                // we only add non-previously existing entries
                if (!Tag::where('external_id', $id)->first()){

                    Log::info("  importing new tag " . $label . " with id " . $id );
                    \DB::table('tags')->insert(
                        array(
                            'external_id' => $id,
                            'name' => '{"en": "'. ucfirst($label) .'"}',
                            'slug' => '{"en": "' . AsciiSlugger::slug($label). '"}',
                            'type' => $groupName,
                            'order_column' => 1,
                    ));
                }

            }
        }
        //
    }
}
