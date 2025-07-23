<?php

namespace App\Traits;

use App\Observers\CordraObserver;
use App\Services\ConnectionWithCordraService;
use Illuminate\Support\Facades\Http;

interface CordraInterface
{
    public function toCordraJson();
}

trait HasCordra
{

    public static function bootHasCordra()
    {
        (new static)->registerObserver(CordraObserver::class);
    }


    public function setToSync()
    {
        $model = get_class($this);
        $cordraId = ConnectionWithCordraService::getPidCordra($this->id, $model);
        ConnectionWithCordraService::insertRelation($model, $this->id, $cordraId, false);
    }
    public function updateSync()
    {
        $model = get_class($this);
        $idCordra = ConnectionWithCordraService::getPidCordra($this->id, $model);
        ConnectionWithCordraService::updateRelation($this->id,$idCordra, false, $model);
    }


    public function saveDataToCordra(): void
    {
        $json = $this->toCordraJson();
        if (isset($json)) {
            $model = get_class($this);
            $entityId = $json['id'];
            $json['id'] = '';
            $json = json_encode($json, true);
            $type = ConnectionWithCordraService::getType($this);
            $idCordra = ConnectionWithCordraService::getPidCordra($this->id, $model);
            if(isset($idCordra)) {
                $url = env('CORDRA_URL') . 'objects/' . $idCordra .env('CORDRA_DRY_RUN', '&dryRun');
                $request = Http::withBasicAuth(env('CORDRA_USERNAME'), env('CORDRA_PASSWORD'))
                    ->withBody($json)
                    ->put($url);
            } else {
                $url = env('CORDRA_URL') . 'objects/?type=' . $type .env('CORDRA_DRY_RUN', '&dryRun');
                $request = Http::withBasicAuth(env('CORDRA_USERNAME'), env('CORDRA_PASSWORD'))
                    ->withBody($json)
                    ->post($url);
            }

            if ($request->status() == 200) {
                $body = $request->body();
                $response = json_decode($body);
                $idCordra = $response->id;
                ConnectionWithCordraService::insertRelation($model, $entityId, $idCordra, true);
            } else {
                ConnectionWithCordraService::insertRelation($model, $entityId, $idCordra, false);
            }
        }
    }

    /*public function updateDataInCordra($entityId): void
    {
        $model = get_class($this);
        $idCordra = ConnectionWithCordraService::getPidCordra($entityId, $model);
        if (isset($idCordra)) {
            $json = $this->toCordraJson();
            if (isset($json)) {
                $entityId = $json['id'];
                $json['id'] = '';
                $json = json_encode($json, true);
                $url = env('CORDRA_URL') . 'objects/' . $idCordra .env('CORDRA_DRY_RUN', '&dryRun');
                $request = Http::withBasicAuth(env('CORDRA_USERNAME'), env('CORDRA_PASSWORD'))
                    ->withBody($json)
                    ->put($url);
                if ($request->status() == 200) {
                    ConnectionWithCordraService::updateRelation($entityId, $idCordra, true, $model);
                } else {
                    ConnectionWithCordraService::updateRelation($entityId, $idCordra, false, $model);
                }
            }
        } else {
            $this->saveDataToCordra();
        }
    }*/


}
