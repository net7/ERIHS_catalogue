<?php

namespace App\Services;


use Illuminate\Support\Facades\DB;

class ConnectionWithCordraService
{

    public static function insertRelation($model, $entityId, $cordraId, $synchronized): void
    {
        $entity = DB::table('cordra_entity_synchronization')
            ->where('entity_id', '=', $entityId)
            ->where('entity_type', '=', $model)
            ->first();
        if (!isset($entity)) {
            DB::table('cordra_entity_synchronization')->insert([
                'entity_id' => $entityId,
                'entity_type' => $model,
                'cordra_id' => $cordraId,
                'synchronized' => $synchronized,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            self::updateRelation($entityId, $cordraId, $synchronized, $model);
        }
    }


    public static function getPidCordra($entityId, $model)
    {
        $entity = DB::table('cordra_entity_synchronization')
            ->where('entity_id', '=', $entityId)
            ->where('entity_type', '=', $model)
            ->first();
        if (isset($entity)) {
            return $entity->cordra_id;
        }

        return null;
    }

    public static function updateRelation($entityId, $cordraId, $synchronized, $model): void
    {
        $entity = DB::table('cordra_entity_synchronization')
            ->where('entity_id', '=', $entityId)
            ->where('entity_type', '=', $model)
            ->first();
        if (isset($entity)) {
            DB::table('cordra_entity_synchronization')
                ->where('entity_id', '=', $entityId)
                ->where('entity_type', '=', $model)
                ->update([
                    'cordra_id' => $cordraId,
                    'synchronized' => $synchronized,
                    'updated_at' => now(),
                ]);
        }
    }

    public static function getType($entity)
    {
        $class = get_class($entity);
        if ($class == 'App\Models\Tool') {
            $type = ucfirst($entity->tool_type);
        } else if ($class == 'App\Models\Organization') {
            $type = 'Organisation';
        } else if ($class == 'App\Models\User') {
            $type = 'Person';
        } else {
            $splitted = explode('\\', $class);
            $type = last($splitted);
        }
        return $type;
    }


}
