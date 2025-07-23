<?php

namespace App\Services;

use App\Models\Method;
use App\Models\Tool;
use Illuminate\Support\Facades\Auth;
use Spatie\Tags\Tag;

class ToolService
{
    public static function createJsonToSend($tool)
    {
        $urls = $tool->url;
        $attributes = self::unsetUselessAttributes($tool->getAttributes(), ['tool_type', 'organization_id', 'created_at', 'updated_at', 'url']);
        $item = $attributes;
        if (isset($urls)) {
            $urlExists = false;
            foreach ($urls as $url) {
                $has_url = [];
                foreach ($url as $key => $value) {
                    if (isset($value)) {
                        $urlExists = true;
                        if (str_ends_with($key, '_tag_field')) {
                            $keyForJson = str_replace('_tag_field', '', $key);
                            $has_url[$keyForJson] = Tag::find($value)->external_id;
                        } else {
                            $has_url[$key] = $value;
                        }
                    }
                }
                if ($urlExists) {
                    $item['has_url'][] = $has_url;
                }
            }
        }
        $tags = $tool->tags()->get();
        foreach ($tags as $tag) {
            $tagType = str_replace('tool_equipment_', '', $tag->type);
            $tagType = str_replace('tool_', '', $tagType);
            $item[$tagType][] = $tag->external_id ?? $tag->name;
        }
        return $item;
    }

    public static function unsetUselessAttributes($attributes, $attributesToUnset)
    {
        foreach ($attributesToUnset as $attribute) {
            unset($attributes[$attribute]);
        }
        return $attributes;
    }

    public static function getMyTools($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        if ($user) {
            return Tool::whereHas('organization', function ($query) use ($user) {
                $query->whereIn('id', $user->organizations()->pluck('organization_id'));
            });
        }

        return null;
    }
}
