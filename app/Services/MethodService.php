<?php

namespace App\Services;

use App\Models\Method;
use App\Models\MethodServiceTool;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Spatie\Tags\Tag;

class MethodService
{

    public static function createJsonToSend($method): array
    {
        $item = self::unsetFields($method->getAttributes(), ['created_at', 'updated_at']);

        $alternativeLabels = $method->alternative_labels;
        $methodParameters = $method->method_parameter;
        $item['alternative_labels'] = [];
        $item['method_parameter'] = [];
        $item['creation_date'] = $method->created_at;
        if (isset($alternativeLabels)) {
            foreach ($alternativeLabels as $label) {
                foreach ($label as $value) {
                    $item['alternative_labels'][] = $value;
                }
            }
        }

        if (isset($methodParameters)) {
            $parametersExists = false;
            foreach ($methodParameters as $parameters) {
                $methodParameter = [];
                $parameter_type = null;
                $parameter_value = null;
                foreach ($parameters as $key => $value) {
                    if ($key == 'parameter_value_type') {
                        continue;
                    }
                    if (isset($value)) {
                        $parametersExists = true;
                        if (str_ends_with($key, '_tag_field')) {
                            $keyForJson = str_replace('_tag_field', '', $key);
                            $tag = Tag::find($value);
                            $methodParameter[$keyForJson] = $tag->external_id ?? $tag->name;
                        } else {
                            $methodParameter[$key] = $value;
                        }
                    }
                }
                if ($parametersExists) {
                    $item['method_parameter'][] = $methodParameter;
                }
            }
        }
        $tags = $method->tags()->get();
        foreach ($tags as $tag) {
            $item[$tag->type][] = $tag->external_id ?? $tag->name;
        }

        $methodServiceTool = MethodServiceTool::where('method_id', '=', $method->id)->get();
        $services = [];
        foreach ($methodServiceTool as $relation) {
            $services[] = $relation->service_id;
        }

        $authors = [];
        foreach ($services as $service) {
            $organizationId = Service::find($service)->organization_id;
            $cordraId = ConnectionWithCordraService::getPidCordra($organizationId, 'App\Models\Organization');
            if (!in_array($cordraId, $authors)) {
                $authors[] = $cordraId;
            }
        }
        $item['authors'] = $authors;
        return $item;
    }

    public static function unsetFields($attributes, $fieldsToRemove)
    {
        foreach ($fieldsToRemove as $field) {
            unset($attributes[$field]);
        }
        return $attributes;
    }


    public static function getMyMethods($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        if ($user) {
            return Method::whereHas('organization', function ($query) use ($user) {
                $query->whereIn('organization_id', $user->organizations()->pluck('organization_id'));
            });
        }

        return null;
    }
}
