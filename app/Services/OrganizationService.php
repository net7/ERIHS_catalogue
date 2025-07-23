<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Tags\Tag;

class OrganizationService
{

    public static function createJsonToSend($organization): array
    {
        $item = self::unsetFields($organization->getAttributes(), ['created_at', 'updated_at', 'webpages', 'external_pid', 'research_disciplines']);

        $externalPids = $organization->external_pid;
        $webpages = $organization->webpages;
        $researchDisciplines = $organization->research_disciplines;
        $researchReferences = $organization->research_references;


        if (isset($externalPids)) {
            $externalPidExists = false;
            foreach ($externalPids as $externalPid) {
                $pid_type = [];
                foreach ($externalPid as $key => $value) {
                    if (isset($value)) {
                        $externalPidExists = true;
                        if (str_ends_with($key, '_tag_field')) {
                            $keyForJson = str_replace('_tag_field', '', $key);
                            $tag = Tag::find($value);
                            $pid_type[$keyForJson] = $tag->external_id ?? $tag->name;
                        } else {
                            $pid_type[$key] = $value;
                        }
                    }
                }
                if ($externalPidExists) {
                    $item['external_pids'][] = $pid_type;
                }
            }
        }

        if (isset($webpages)) {
            $item['webpages'] = [];
            foreach ($webpages as $webPage) {
                foreach ($webPage as $value) {
                    if (isset($value)) {
                        $item['webpages'][] = $value;
                    }
                }
            }
        }

        if (isset($researchDisciplines)) {
            $item['research_disciplines'] = [];
            foreach ($researchDisciplines as $discipline) {
                foreach ($discipline as $value) {
                    if (isset($value)) {
                        $tag = Tag::find($value);
                        $item['research_disciplines'][] = $tag->external_id ?? $tag->name;
                    }
                }
            }
        }

        if (isset($researchReferences)) {
            $item['research_references'] = [];
            $researchReferencesExists = false;
            foreach ($researchReferences as $reference) {
                $research_reference = [];
                foreach ($reference as $key => $value) {
                    if (isset($value)) {
                        $researchReferencesExists = true;
                        if (str_ends_with($key, '_tag_field')) {
                            $keyForJson = str_replace('_tag_field', '', $key);
                            $tag = Tag::find($value);
                            $research_reference[$keyForJson] = $tag->external_id ?? $tag->name;
                        } else {
                            $research_reference[$key] = $value;
                        }
                    }
                }
                if ($researchReferencesExists) {
                    $item['research_references'][] = $research_reference;
                }
            }
        }

        $tags = $organization->tags()->get();
        foreach ($tags as $tag) {
            $item[$tag->type][] = $tag->external_id ?? $tag->name;
        }
        return $item;
    }

    public static function unsetFields($attributes, $fieldsToRemove)
    {
        foreach ($fieldsToRemove as $field) {
            unset($attributes[$field]);
        }
        return $attributes;
    }

}
