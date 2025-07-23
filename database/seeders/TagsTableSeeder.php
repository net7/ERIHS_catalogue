<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Facades\Symfony\Component\String\Slugger\AsciiSlugger;

class TagsTableSeeder extends Seeder
{

    /**
     * Tags seed file
     *
     * @return void
     */

    public function run()
    {
        $tags = [
            'tool_equipment_acquisition_areas' => [
                "small spot",
                "large spot",
                "micro",
                "macro",
                "large area",
            ],
            'method_parameter_unit' => [
                "none",
                "cm",
                "nm",
                "counts",
                "Pa",
                "bar",
            ],
            'method_parameter_type' => [
                "working distance",
                "wavelength",
                "signal count",
                "pressure",
                "vacuum",
            ],
            'organisation_type' => [
                "Research organisation",
                "University",
                "Public body",
                "Private body",
            ],
            'tool_software_input_data_types' => [
                "Text",
                "Text/numbers",
                "Text/dates",
                "Text/variables",
                "Image",
                "Image/RGB",
                "Image/Gray scale",
                "Image/Multi band image",
                "Image/False color",
                "Image/Drawing",
                "Image/Figure",
                "Dataset",
                "Dataset/2D",
                "Dataset/3D",
                "Dataset/Time series",
                "Dataset/Data package (images, spectra, other)",
                "Model",
                "Model/3D",
                "Report",
                "Audio/video",
            ],
            'tool_output_data_types' => [
                "Image",
                "Image/RGB",
                "Image/Gray scale",
                "Image/Multi band image",
                "Image/False color",
                "Image/Drawing",
                "Image/Figure",
                "Dataset",
                "Dataset/2D",
                "Dataset/3D",
                "Dataset/Time series",
                "Dataset/Data package (images, spectra, other)",
                "Model",
                "Model/3D",
                "Report",
                "Audio/video",
            ],
            'tool_url_type' => [
                "website",
                "documentation",
                "source code",
                "demo",
                "online resource",
                "licence",
                "other",
            ]
        ];

        \DB::table('tags')->delete();

        foreach($tags as $type => $typedTags ) {
            foreach ($typedTags as $tag){
                \DB::table('tags')->insert(
                    array(
                        'external_id' => null,
                        'name' => '{"en": "'. $tag .'"}',
                        'slug' => '{"en": "' . AsciiSlugger::slug($tag). '"}',
                        'type' => $type,
                        'order_column' => 1,
                ));
            }
        }
      
    }
}
