<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Spatie\Tags\Tag;

class ServiceService
{


    public static function getMyServicesQuery($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        if ($user) {
            return Service::whereHas('organization', function ($query) use ($user) {
                $query->whereIn('id', $user->organizations()->pluck('organization_id'));
            });
        }

        return null;
    }

    public static function createJsonToSend($service)
    {

        $contacts = $service->contacts;
        $functions = $service->functions;
        $measurable_properties = $service->measurable_properties;
        $links = $service->links;
        $categories = $service->categories;


        $item = self::unsetUselessAttributes($service->getAttributes(),
            ['created_at', 'updated_at',]);
        $item['creation_date'] = $service->created_at;

        if (isset($contacts)) {
            $item['contacts'] = [];
            $item = self::getFromRepeater($contacts, $item, 'contacts');
        }
        if (isset($categories)) {
            $item['categories'] = [];
            foreach ($categories as  $category) {
                foreach($category as  $catItem) {
                    $item['categories'][] = $catItem;
                }
            }
        }


        if (isset($functions)) {
            $item['functions'] = [];
            foreach ($functions as  $function) {
                foreach($function as  $funItem) {
                    $item['functions'][] = $funItem;
                }
            }
        }

        if (isset($measurable_properties)) {
            $item['measurable_properties'] = [];
            $item = self::getFromRepeater($measurable_properties, $item, 'measurable_properties');
        }

        if (isset($links)) {
            $item['links'] = [];
            $item = self::getFromRepeater($links, $item, 'links');
        }

        $tags = $service->tags()->get();
        foreach ($tags as $tag) {
            $tagType = $tag->type;
            if($tagType == 'period_unit') {
                $tagType = 'access_unit';
            }
            if($tagType == 'readiness_level' || $tagType == 'access_unit') {
                $item[$tagType] = $tag->external_id ?? $tag->name;
            } else {
                $item[$tagType][] = $tag->external_id ?? $tag->name;
            }
        }
        $personRoleTag = Tag::query()
            ->where('type', '=', 'person_role')
            ->where('name->en', '=', 'administrative support')
            ->first();
        $memberId = $service->serviceManagers->first()->id;

        $cordraUser = ConnectionWithCordraService::getPidCordra($memberId,'App\Models\User');
        $item['team_members'] = [];
        $item['team_members']['team_member_id'] = $cordraUser->cordra_id ?? '';
        $item['team_members']['roles'] = $personRoleTag->external_id;
        $created =  $service->created_at;
        $item['team_members']['start_date'] = $created->format('Y-m-d');
        $methodServiceTools = $service->methodServiceTool;
        $methods = $methodServiceTools->pluck('method_id');;
        $cordraIds = [];
        foreach ($methods as $methodId) {
            $cordraEntity = ConnectionWithCordraService::getPidCordra($methodId, 'App\Models\Method');
            $cordraIds[] = $cordraEntity->cordra_id ?? '';
        }
        $item['application_required'] = (bool)$service->application_required;
        $item['service_active'] = (bool)$service->service_active;
        $item['methods'] = $cordraIds;


        return $item;
    }

    public static function getServicesForProposals(){
        return Service::where('service_active', '1')
                ->where('application_required','1');
    }

    public static function unsetUselessAttributes($attributes, $attributesToUnset)
    {
        foreach ($attributesToUnset as $attribute) {
            unset($attributes[$attribute]);
        }
        return $attributes;
    }


    public static function getFromRepeater($array, $item, $keyToUse): array
    {
        $elementExists = false;
        foreach ($array as $element) {
            $res = [];
            foreach ($element as $key => $value) {
                $elementExists = false;
                if (isset($value)) {
                    $elementExists = true;
                    if (str_ends_with($key, '_tag_field')) {
                        $keyForJson = str_replace('_tag_field', '', $key);
                        if($keyForJson == 'materials') {
                            foreach ($value as $tag_id){
                                $res[$keyForJson][] = $tag_id ;
                            }
                        }
                        else {
                            $tag = Tag::find($value);

                            $res[$keyForJson] = $tag->external_id ?? $tag->name;
                        }
                    } else {
                        if($key == 'materials_other') {
                            $res[$key][] = $value;
                        } else {
                            $res[$key] = $value;
                        }
                    }
                }
            }
            if ($elementExists) {
                $item[$keyToUse][] = $res;
            }
        }
        return $item;
    }
}
