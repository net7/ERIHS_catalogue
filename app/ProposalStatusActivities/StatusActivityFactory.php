<?php

namespace App\ProposalStatusActivities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StatusActivityFactory
{

    public static function getStatusActivity(Model $model, array $data = [])
    {

        $timestamp = Arr::pull($data, 'timestamp');
        $type = Arr::pull($data, 'type');
        $statusKey = Arr::pull($data, 'statusKey');

        $activity =  __NAMESPACE__ . "\\" . Str::studly($type) . 'StatusActivity';

        $activityData = Arr::get($data,'data',[]);
        if(class_exists($activity)) {
            $object = new $activity(...$activityData);
        } else {
            throw new \Exception("Invalid activity.");
        }

        $object->setStatusKey($statusKey);
        $object->setModel($model);
        $object->setTimestamp($timestamp);
        return $object;
    }




}
