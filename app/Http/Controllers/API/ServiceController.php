<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Method;
use App\Models\MethodServiceTool;
use App\Models\Tool;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Tags\Tag;

class ServiceController extends Controller
{
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $this->validateRequiredFieldsInArray($request);
        if ($validated !== 'ok') {
            return response()->json("The field $validated must not be empty", 500);
        }

        if (isset($request->methodsAndTools)) {
            $validatedMethodAndTools = $this->validateRequiredFieldsInArray($request, true);
            if ($validatedMethodAndTools !== 'ok') {
                return response()->json("The field $validatedMethodAndTools must not be empty", 500);
            }
        }


        $service = Service::create($this->getServiceData($request));

        if (!$service) {
            return response()->json('The service was not created. Please try again later', 500);
        }

        $this->attachTags($service, $request);

        $this->attachMethodsAndTools($service, $request);

        $service->save();
        // return response()->json("Service " . $service->title . " with ID " . $service->id . " was created successfully", 201);
        return response()->json(
            [
                'message' => 'Service \''. $service->title . '\' successfully created',
                'id' => $service->id
            ], 201);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $service = Service::findOrFail($id);

        $validatedMethodAndTools = $this->validateRequiredFieldsInArray($request, true);
        if ($validatedMethodAndTools !== 'ok') {
            return response()->json("The field $validatedMethodAndTools in methodsAndTools must not be empty", 500);
        }

        // TODO: check if the user is the owner of the record, return 403 - Forbidden - otherwise

        $serviceData = $this->getServiceData($request, $service);
        $service->fill($serviceData);

        $this->updateTags($service, $request);
        $this->updateMethodsAndTools($service, $request);

        $service->save();
        // return response()->json("Service " . $service->title . "with ID " . $service->id . " was updated successfully", 200);
        return response()->json(
            [
                'message' => 'Service \''. $service->title . '\' successfully updated',
                'id' => $service->id
            ], 200);
    }


    private function getServiceData(Request $request, Service $service = null): array
    {
        $data = [
            'title' => $request->title ?? $service->title,
            // 'service_manager_id' => $request->service_manager_id ?? $service->service_manager_id,
            'organization_id' => $request->organization_id ?? $service->organization_id,
            'summary' => $request->summary ?? $service->summary,
            'description' => $request->description ?? $service->description,
            'application_required' => $request->application_required ?? $service->application_required,
            'limitations' => $request->limitations ?? $service->limitations,
            'version' => $request->version ?? $service->version,
            'version_date' => $request->version_date ?? $service->version_date,
            //'checked_date' => $request->checked_date ?? $service->checked_date,
            'output_description' => $request->output_description ?? $service->output_description,
            'input_description' => $request->input_description ?? $service->input_description,
            'further_comments' => $request->further_comments ?? $service->further_comments,
            'hours_per_unit' => $request->hours_per_unit ?? $service->hours_per_unit,
            'access_unit_cost' => $request->access_unit_cost ?? $service->access_unit_cost,
            'service_active' => $request->service_active ?? $service->service_active,
            'url' => $request->url ?? $service->url,
            'creation_date' => $service ? $service->creation_date : now(),
        ];

        if ($request->has('categories')) $data['categories'] = self::saveCategories($request->categories);
        if ($request->has('functions')) $data['functions'] = self::saveFunctions($request->functions);

        return $data;
    }

    private function attachTags(Service $service, Request $request): void
    {
        $tagGroups = [
            'e_rihs_platform' => 'e-rihs_platform',
            'access_unit' => 'period_unit',
            'readiness_level' => 'readiness_level',
            'operating_languages' => 'operating_language',
            'research_disciplines' => 'research_disciplines',
            'techniques' => 'technique',
            'provider_role' => 'provider_role'
        ];

        foreach ($tagGroups as $field => $type) {
            $this->attachTagGroup($service, $request->$field ?? [], $type);
        }

        $this->attachMeasurableProperties($service, $request->measurable_properties ?? []);
        $service->contacts = $this->getContacts($request->contacts ?? []);
        $service->links = $this->getLinks($service, $request->links ?? []);
    }

    private function updateTags(Service $service, Request $request): void
    {
        $tagGroups = [
            'e_rihs_platform' => 'e-rihs_platform',
            'access_unit' => 'period_unit',
            'readiness_level' => 'readiness_level',
            'operating_languages' => 'operating_language',
            'research_disciplines' => 'research_disciplines',
            'techniques' => 'technique',
            'provider_role' => 'provider_role'
        ];

        foreach ($tagGroups as $field => $type) {
            if ($request->has($field)) {
                $oldTags = $service->tags()->where('type', '=', $type)->get();
                $service->detachTags($oldTags);
                $this->attachTagGroup($service, $request->$field, $type);
            }
        }

        if ($request->has('measurable_properties')) {
            $service->measurable_properties = [];
            $this->attachMeasurableProperties($service, $request->measurable_properties);
        }

        if ($request->has('contacts')) {
            $service->contacts = $this->getContacts($request->contacts);
        }

        if ($request->has('links')) {
            $service->links = $this->getLinks($service, $request->links);
        }
    }

    private function attachTagGroup($object, $items, string $type): void
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                $tag = Tag::findFromString(ucfirst($item), $type);
                $object->attachTag($tag);
            }
        } elseif ($items) {
            $tag = Tag::findFromString($items, $type);
            $object->attachTag($tag);
        }
    }

    private function attachMeasurableProperties(Service $service, array $measurableProperties): void
    {
        $properties = [];
        foreach ($measurableProperties as $mp) {
            $tmp = [];
            if (isset($mp['measurable_property'])) {
                $measurableProperty = Tag::findFromString($mp['measurable_property'], 'measurable_property');
                $tmp['class_tag_field'] = $measurableProperty->id;
                $service->attachTag($measurableProperty);
            }
            if (isset($mp['materials'])) {
                $materials = Tag::findFromString($mp['materials'], 'material');
                $tmp['materials_tag_field'] = $materials->id;
                $service->attachTag($materials);
            }
            if (isset($mp['other_materials'])) {
                $tmp['materials_other'] = $mp['other_materials'];
            }
            $properties[] = $tmp;
        }
        $service->measurable_properties = $properties;
    }

    private function getContacts(array $contacts): array
    {
        $contactList = [];
        foreach ($contacts as $contact) {
            $contactList[] = array_filter([
                'email' => $contact['email'] ?? null,
                'phone' => $contact['phone'] ?? null,
            ]);
        }
        return $contactList;
    }

    private function getLinks(Service $service, array $links): array
    {
        $linkList = [];
        foreach ($links as $link) {
            $linkType = Tag::findFromString($link['link_type'], 'link_type');
            $service->attachTag($linkType);
            $linkList[] = [
                'url' => $link['url'],
                'type_tag_field' => $linkType->id,
            ];
        }
        return $linkList;
    }

    private function attachMethodsAndTools(Service $service, Request $request): void
    {
        if (isset($request->methodsAndTools)) {
            foreach ($request->methodsAndTools as $methodAndTool) {
                $method = $methodAndTool['method'];
                $newMethod = Method::create([
                    'preferred_label' => $method['title'],
                    'method_version' => $method['method_version']
                ]);
                $this->attachTagGroup($newMethod, $method['relevant_technique'], 'technique');

                $tool = $methodAndTool['tool'];
                $newTool = Tool::create([
                    'name' => $tool['name'],
                    'organization_id' => $tool['organization_id'],
                    'description' => $tool['description'],
                    // 'potential_results' => $tool['potential_result'],
                    'last_checked_date' => $tool['last_checked_date'],
                    'tool_type' => $tool['tool_type']
                ]);

                $licenceType = Tag::findFromString(ucfirst($tool['licence_type']), 'licence_type');
                $newTool->attachTag($licenceType);
                $this->attachTagGroup($newTool, $tool['output_data_types'], 'tool_output_data_types');
                MethodServiceTool::create([
                    'tool_id' => $newTool->id,
                    'method_id' => $newMethod->id,
                    'service_id' => $service->id
                ]);
            }
        }
    }


    private function updateMethodsAndTools(Service $service, Request $request): void
    {
        if (isset($request->methodsAndTools)) {
            MethodServiceTool::where('service_id', $service->id)->delete();

            foreach ($request->methodsAndTools as $methodAndTool) {
                $method = $methodAndTool['method'];
                $newMethod = Method::create([
                    'preferred_label' => $method['title'],
                    'method_version' => $method['method_version']
                ]);
                $this->attachTagGroup($newMethod, $method['relevant_technique'], 'technique');

                $tool = $methodAndTool['tool'];
                $newTool = Tool::create([
                    'name' => $tool['name'],
                    'organization_id' => $tool['organization_id'],
                    'description' => $tool['description'],
                    // 'potential_results' => $tool['potential_result'],
                    'last_checked_date' => $tool['last_checked_date']
                ]);

                $licenceType = Tag::findFromString(ucfirst($tool['licence_type']), 'licence_type');
                $newTool->attachTag($licenceType);
                $this->attachTagGroup($newTool, $tool['output_data_types'], 'tool_output_data_types');
                $this->attachTagGroup($newTool, $tool['output_data_types'], 'tool_output_data_types');
                MethodServiceTool::create([
                    'tool_id' => $newTool->id,
                    'method_id' => $newMethod->id,
                    'service_id' => $service->id
                ]);
            }
        }
    }


    public static function saveCategories($categories): array
    {
        return array_map(fn($category) => ['category' => $category], $categories ?? []);
    }

    public static function saveFunctions($functions): array
    {
        return array_map(fn($function) => ['function' => $function], $functions ?? []);
    }


    public function validateRequiredFieldsInArray($request, $methodsAndTools = false): string
    {
        $requiredFields = [
            'title', 'readiness_level', 'operating_languages',
            'access_unit_cost', 'e_rihs_platform', 'research_disciplines',
            // 'service_manager_id',
            'description', 'summary', 'functions',
            'application_required', 'service_active'
        ];

        if ($methodsAndTools) {
            if (isset($request->methodsAndTools)) {
                foreach ($request->methodsAndTools as $methodAndTool) {
                    $method = $methodAndTool['method'];
                    $tool = $methodAndTool['tool'];

                    if (!isset($method['title'])) {
                        return 'title in method';
                    }
                    if (!isset($method['relevant_technique'])) {
                        return 'relevant_technique in method';
                    }
                    if (!isset($method['method_version'])) {
                        return 'method_version in method';
                    }

                    if (!isset($tool['name'])) {
                        return 'name in tool';
                    }
                    if (!isset($tool['organization_id'])) {
                        return 'organization_id in tool';
                    }
                    if (!isset($tool['description'])) {
                        return 'description in tool';
                    }
                    // if (!isset($tool['potential_result'])) {
                    //     return 'potential_result in tool';
                    // }
                    if (!isset($tool['output_data_types'])) {
                        return 'output_data_types in tool ';
                    }
                    if (!isset($tool['last_checked_date'])) {
                        return 'last_checked_date in tool';
                    }

                    if (!isset($tool['tool_type'])) {
                        return 'tool_type in tool';
                    }
                }
            }
        } else {
            foreach ($requiredFields as $field) {
                if (!isset($request->$field)) {
                    return $field;
                }
            }

            if (isset($request->links)) {
                foreach ($request->links as $link) {
                    if (!isset($link['url'])) {
                        return 'url in links';
                    }
                    if (!isset($link['link_type'])) {
                        return 'link_type in links';
                    }
                }
            }
        }

        return 'ok';
    }


    public function getSchema(): array
    {
        $table = (new Service())->getTable();
        $columns = Schema::getColumnListing($table);

        $columnsWithType = array_combine($columns, array_map(fn($column) => [
            'type' => DB::getSchemaBuilder()->getColumnType($table, $column)
        ], $columns));

        foreach ($columnsWithType as $column => &$properties) {
            if ($this->isRequiredColumn($column)) {
                $properties['required'] = true;
            }
        }

        $columnsWithType['access_unit'] = [
            'type' => 'enum',
            'ref' => 'https://research.ng-london.org.uk/ecls/?group=g12',
            'required' => true
        ];
        $columnsWithType['operating_languages'] = [
            'type' => 'array',
            'ref' => 'https://research.ng-london.org.uk/ecls/?group=g13',
            'required' => true
        ];
        $columnsWithType['readiness_level'] = [
            'type' => 'enum',
            'ref' => 'https://research.ng-london.org.uk/ecls/?group=g5',
            'required' => true
        ];
        $columnsWithType['provider_role'] = [
            'type' => 'array',
            'ref' => 'https://research.ng-london.org.uk/ecls/?group=g15',
            'required' => true
        ];
        $columnsWithType['research_disciplines'] = [
            'type' => 'array',
            'ref' => 'https://research.ng-london.org.uk/ecls/?group=g10',
            'required' => true
        ];
        $columnsWithType['categories'] = ['type' => 'array'];
        $columnsWithType['functions'] = ['type' => 'array'];
        $columnsWithType['contacts'] = [
            'type' => 'array',
            'fields' => [
                'email' => ['type' => 'string'],
                'phone_number' => ['type' => 'string']
            ]
        ];
        $columnsWithType['techniques'] = [
            'type' => 'array',
            'ref' => 'https://research.ng-london.org.uk/ecls/?group=g22'
        ];
        $columnsWithType['measurable_properties'] = [
            'type' => 'array',
            'fields' => [
                'measurable_property' => [
                    'type' => 'enum',
                    'ref' => 'https://research.ng-london.org.uk/ecls/?group=g40'
                ],
                'materials' => [
                    'type' => 'enum',
                    'ref' => 'https://research.ng-london.org.uk/ecls/?group=g35'
                ],
                'other_materials' => ['type' => 'string']
            ]
        ];

        $columnsWithType['links'] = [
            'type' => 'array',
            'fields' => [
                'url' => ['type' => 'string', 'required' => true],
                'link_type' => [
                    'type' => 'enum',
                    'ref' => 'https://research.ng-london.org.uk/ecls/?group=g11',
                    'required' => true
                ]
            ]
        ];
        $columnsWithType['methodsAndTools'] = [
            'type' => 'array',
            'fields' => [
                'method' => [
                    'title' => [
                        'type' => 'string',
                        'required' => true
                    ],
                    'relevant_technique' => [
                        'type' => 'enum',
                        'ref' => 'https://research.ng-london.org.uk/ecls/?group=g22',
                        'required' => true
                    ],
                    'method_version' => [
                        'type' => 'string',
                        'required' => true
                    ]
                ],
                'tool' => [
                    'name' => [
                        'type' => 'string',
                        'required' => true
                    ],
                    'organization_id' => [
                        'type' => 'int',
                        'required' => true
                    ],
                    'description' => [
                        'type' => 'mediumText',
                        'required' => true
                    ],
                    // 'potential_result' => [
                    //     'type' => 'mediumText',
                    //     'required' => true
                    // ],
                    'output_data_types' => [
                        'type' => 'array',
                        'required' => true,
                        'ref' => 'https://research.ng-london.org.uk/ecls/?group=g48'
                    ],
                    'last_checked_date' => [
                        'type' => 'date',
                        'required' => true
                    ],
                    'tool_type' => [
                        'type' => 'enum',
                        'ref' => '[equipment, software]',
                        'required' => true
                    ],
                    'licence_type' => [
                        'type' => 'enum',
                        'ref' => 'https://research.ng-london.org.uk/ecls/?group=g44'
                    ],
                ]
            ]
        ];
        $columnsWithType['service_active']['required'] = true;
        $columnsWithType['application_required']['required'] = true;
        $columnsWithType['e_rihs_platform'] = [
            'type' => 'array',
            'ref' => 'https://research.ng-london.org.uk/ecls/?group=g7',
            'required' => true
        ];
        $columnsWithType['organization_id']['required'] = true;

        unset(
            $columnsWithType['created_at'],
            $columnsWithType['updated_at'],
            $columnsWithType['id'],
            // $columnsWithType['second_service_manager_id']
        );

        return $columnsWithType;
    }

    private function isRequiredColumn(string $column): bool
    {
        $requiredColumns = [
            'title', 'description', 'access_unit', 'operating_languages',
            'readiness_level', 'research_disciplines',
            // 'service_manager_id',
            'service_active', 'application_required', 'organization_id'
        ];

        return in_array($column, $requiredColumns);
    }
}
