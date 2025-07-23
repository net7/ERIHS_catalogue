<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Service;
use Database\Seeders\TagsTableSeeder;
use Database\Seeders\VocabularySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Tags\Tag;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        (new TagsTableSeeder())->run();
        // (new UpdateVocabularySeeder())->run();
        (new VocabularySeeder())->run();


    }

    /**
     * Test API access with a token.
     *
     * @return void
     */
    public function test_authenticated_user_can_access_protected_route()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        // Generate a token for the user
        $token = $user->createToken('Test Token')->plainTextToken;

        // Use the token to authenticate
        Sanctum::actingAs($user);

        // Perform a request to the protected route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        // Assert that the response status is 200 and contains the user data
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_authenticated_user_can_get_service_schema()
    {
        $user = User::factory()->create();
        $user->assignRole(User::SERVICE_MANAGER);

        // Generate a token for the user
        $token = $user->createToken('Test Token')->plainTextToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/service-schema');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_service()
    {
        Organization::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(User::SERVICE_MANAGER);

        // Generate a token for the user
        $token = $user->createToken('Test Token')->plainTextToken;

        // Prepare the complex JSON data for the service creation
        $serviceData = [
            "methodsAndTools" => [
                [
                    "method" => [
                        "title" => "Method test",
                        "method_version" => "12334",
                        "relevant_technique" => "other"
                    ],
                    "tool" => [
                        "name" => "Tool test",
                        "organization_id" => 1,
                        "description" => "Tool test",
                        // "potential_result" => "Tool test",
                        "output_data_types" => ["Image", "Model"],
                        "last_checked_date" => "2024-08-02",
                        "tool_type" => 'software',
                        "licence_type" => 'proprietary licence'
                    ]
                ]
            ],
            "title" => "Service Test",
            "summary" => "test",
            "description" => "test",
            "limitations" => "test",
            "contacts" => [
                [
                    "email" => "test",
                    "phone" => "000000"
                ]
            ],
            "categories" => [
                "test",
                "test 2"
            ],
            "functions" => [
                "test ",
                "test 2"
            ],
            "measurable_properties" => [
                [
                    "measurable_property" => "age",
                    "materials" => "tweed",
                    "other_materials" => "test"
                ],
                [
                    "measurable_property" => "age",
                    "materials" => "tweed",
                    "other_materials" => "test"
                ]
            ],
            "links" => [
                [
                    "url" => "test",
                    "link_type" => "other"
                ]
            ],
            "e_rihs_platform" => [
                "digilab"
            ],
            "version" => "1.0",
            "version_date" => "2024-07-31",
            "checked_date" => "2024-07-31",
            "output_description" => "test",
            "input_description" => "test",
            "further_comments" => "test",
            "hours_per_unit" => "24",
            "access_unit_cost" => "34",
            "url" => "test",
            "organization_id" => "1",
            "service_manager_id" => "1",
            "application_required" => false,
            "service_active" => true,
            "readiness_level" => "1 - basic principles observed",
            "operating_languages" => [
                "bg - bulgarian"
            ],
            "research_disciplines" => [
                "acoustics"
            ],
            "access_unit" => "day",
            "techniques" => [
                "other",
                "3d computed microtomography (µct)"
            ]
        ];

        // Perform a request to the service creation route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/create-service', $serviceData);

        // Assert that the response status is 201 (created)
        $response->assertStatus(201);
        preg_match('/Service .* with ID (\d+) was created successfully/', $response->getContent(), $matches);
        $serviceId = $matches[1] ?? null;

        // Ensure we have extracted the service ID
        $this->assertNotNull($serviceId, "Service ID should not be null");

        // Assert that the service exists in the database
        $this->assertDatabaseHas('services', [
            'title' => $serviceData['title'],
            'summary' => $serviceData['summary'],
            'description' => $serviceData['description'],
            'limitations' => $serviceData['limitations'],
            'version' => $serviceData['version'],
            'version_date' => $serviceData['version_date'],
            'checked_date' => $serviceData['checked_date'],
            'output_description' => $serviceData['output_description'],
            'input_description' => $serviceData['input_description'],
            'further_comments' => $serviceData['further_comments'],
            'hours_per_unit' => $serviceData['hours_per_unit'],
            'access_unit_cost' => $serviceData['access_unit_cost'],
            'url' => $serviceData['url'],
            'organization_id' => $serviceData['organization_id'],
            'service_manager_id' => $serviceData['service_manager_id'],
            'application_required' => $serviceData['application_required'],
            'service_active' => $serviceData['service_active'],
        ]);


        // Assert methods and tools
        foreach ($serviceData['methodsAndTools'] as $methodsAndToolsData) {


            $this->assertDatabaseHas('methods', [
                'preferred_label' => $methodsAndToolsData['method']['title'],
                'method_version' => $methodsAndToolsData['method']['method_version'],
            ]);

            $this->assertDatabaseHas('tools', [
                'name' => $methodsAndToolsData['tool']['name'],
                'organization_id' => $methodsAndToolsData['tool']['organization_id'],
                'description' => $methodsAndToolsData['tool']['description'],
                // 'potential_results' => $methodsAndToolsData['tool']['potential_result'],
                'last_checked_date' => $methodsAndToolsData['tool']['last_checked_date'],
            ]);
        }

        $service = Service::find(intval($serviceId));
        $expectedCategories = array_map(fn($category) => ['category' => trim($category)], $serviceData['categories']);
        $expectedFunctions = array_map(fn($function) => ['function' => trim($function)], $serviceData['functions']);
        $this->assertEquals( json_encode($service->contacts), json_encode($serviceData['contacts']));
        $this->assertEquals(json_encode($service->categories), json_encode($expectedCategories));
        $this->assertEquals(json_encode($service->functions), json_encode($expectedFunctions));


        $measurableProperties = $service->measurable_properties;
        $i=0;
        foreach($measurableProperties as $mp) {
            $classTagField = $mp['class_tag_field'];
            $measurableProperty = Tag::find($classTagField)->slug;
            $this->assertEquals($measurableProperty, $serviceData['measurable_properties'][$i]['measurable_property']);

            $materialTagField = $mp['materials_tag_field'];
            $material =  Tag::find($materialTagField)->slug;
            $this->assertEquals($material, $serviceData['measurable_properties'][$i]['materials']);

            $this->assertEquals($mp['materials_other'], $serviceData['measurable_properties'][$i]['other_materials']);
        }

        $i=0;
        $links = $service->links;

        foreach($links as $link) {
            $linkTypeTag = $link['type_tag_field'];
            $linkType = Tag::find($linkTypeTag)->slug;

            $this->assertEquals($linkType, $serviceData['links'][$i]['link_type']);
            $this->assertEquals($link['url'], $serviceData['links'][$i]['url']);
        }

    }


    public function test_authenticated_user_can_update_service()
    {
        $user = User::factory()->create();
        $user->assignRole(User::SERVICE_MANAGER);

        Organization::factory()->create();
        Service::factory()->create();

        // Generate a token for the user
        $token = $user->createToken('Test Token')->plainTextToken;

        $serviceData = ['title' => 'testUpdate'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/update-service/1', $serviceData);

        // Assert that the response status is 201 (created)
        $response->assertStatus(200);

        $this->assertDatabaseHas('services', [
            'title' => $serviceData['title']
        ]);
    }

}
