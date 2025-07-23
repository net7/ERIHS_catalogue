<?php

namespace App\Traits;

use App\ProposalStatusActivities\StatusActivityFactory;
use App\ProposalStatusActivities\StatusActivityInterface;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait FSMTrait
{
    use \Gecche\FSM\FSMTrait;

    public function getActivitiesFieldname()
    {
        return 'activities';
    }

    public function getCasts()
    {
        $this->casts[$this->getActivitiesFieldname()] = 'array';
        $this->casts[$this->getStatusHistoryFieldname()] = 'array';
        return parent::getCasts();
    }


    protected function updateStatusHistory($statusCode, $statusData, $prevStatusCode = null, $params = [])
    {
        $statusHistoryFieldname = $this->getStatusHistoryFieldname();
        $states = $this->$statusHistoryFieldname;

        if (is_null($states)) {
            //INIZIALIZZO STATI E ACTIVITIES (NON CONTEMPLO ATTIVITA' SENZA LO STATO INIZIALE)
            $states = [];
            $activitiesFieldname = $this->getActivitiesFieldname();
            $this->$activitiesFieldname = [];
        }
        $statusInfo = $this->buildStatusInfo($statusCode, $statusData, $prevStatusCode, $params);

        $states[] = $statusInfo;
        $this->$statusHistoryFieldname = $states;
    }

    protected function buildStatusInfo($statusCode, $statusData, $prevStatusCode, $params)
    {
        return [
            'timestamp' => Carbon::now()->toDateTimeString(),
            'status_code' => $statusCode,
            'info' => $statusData,
            'position' => $this->getLastStatusKey() + 1,
        ];
    }


    public function getLastStatus()
    {
        $statusHistory = $this->getStatusHistory();
        return Arr::last($statusHistory, null, []);
    }

    public function getLastStatusKey()
    {
        $statusHistory = $this->getStatusHistory();
        if (!$statusHistory) {
            return -1;
        }
        return array_key_last($statusHistory);
    }

    public function addActivity(StatusActivityInterface $activityObject, $statusKey = null, $save = false, $saveOptions = [])
    {
        $activitiesFieldname = $this->getActivitiesFieldname();
        $activities = $this->$activitiesFieldname;

        $activity = $this->buildActivity($activityObject, $statusKey);
        $activities[] = $activity;
        $this->$activitiesFieldname = $activities;

        if ($save) {
            $this->save($saveOptions);
        }
    }

    public function addActivityAndSave(StatusActivityInterface $activityObject, $statusKey = null, $saveOptions = [])
    {
        $this->addActivity($activityObject, $statusKey, true, $saveOptions);
    }

    protected function buildActivity(StatusActivityInterface $activityObject, $statusKey = null)
    {
        if (is_null($statusKey)) {
            $statusKey = $this->getLastStatusKey();
        }

        $activity = array_merge($activityObject->toArray(), [
            'statusKey' => $statusKey,
        ]);

        return $activity;
    }

    public function getActivities()
    {
        $activities = $this->getActivitiesFieldname();
        return $this->$activities;
    }


    public function getLastStatusActivities()
    {
        $lastStatusKey = $this->getLastStatusKey();
        $activities = $this->getActivities();
        return Arr::where($activities, function ($item) use ($lastStatusKey) {
            return Arr::get($item,'statusKey') == $lastStatusKey;
        });

    }

    public function getLastActivity()
    {
        $lastStatusActivities = $this->getLastStatusActivities();
        return Arr::last($lastStatusActivities, null, []);
    }

    public function getAllActivities($timeline = 'DESC')
    {
        $activitiesArray = $this->getActivities();
        return $this->getActivityCollectionFromArray($activitiesArray);
    }

    public function getStatusActivities($statusKey = null)
    {
        if (is_null($statusKey)) {
            $statusKey = $this->getLastStatusKey();
        }
        $activities = collect($this->getActivities());
        $activitiesArray = $activities->where('statusKey', $statusKey)->all();

        return $this->getActivityCollectionFromArray($activitiesArray);
    }


    public function getStatusHistoryWithActivities($activitiesDataCallback = null, $activitiesDataCallbackParams = [], $reverse = false)
    {
        $statusHistory = $this->getStatusHistory();
        if (!$statusHistory){
            return [];
        }
        foreach ($statusHistory as $statusKey => $statusData) {
            $statusHistory[$statusKey]['activities'] = $this->getStatusActivities($statusKey);
            if (is_string($activitiesDataCallback)) {
                $tmp=
                    ($statusHistory[$statusKey]['activities'])->map(
                        function ($item) use ($activitiesDataCallback, $activitiesDataCallbackParams) {
                            return $item->$activitiesDataCallback(...$activitiesDataCallbackParams);
                        });
                if ($reverse) {
                    $tmp = $tmp->reverse();
                }
                $statusHistory[$statusKey]['activities_data'] = $tmp;
            }
        }

        return $statusHistory;
    }

    protected function getActivityObjectFromData($activityData = [])
    {
        return StatusActivityFactory::getStatusActivity($this, $activityData);
    }

    protected function getActivityCollectionFromArray($activitiesArray, $timeline = 'ASC')
    {
        $activitiesCollection = new Collection();
        foreach ($activitiesArray as $activityItem) {
            if ($timeline == 'ASC') {
                $activitiesCollection->add($this->getActivityObjectFromData($activityItem));
            } else {
                $activitiesCollection->prepend($this->getActivityObjectFromData($activityItem));
            }
        }
        return $activitiesCollection;
    }

    public function hasBeenInStatus($statusCode)
    {
        $history = $this->getStatusHistory();
        if (is_null($history)) {
            return false;
        }
        $passedStates = Arr::pluck($history, 'status_code', 'position');
        return in_array($statusCode, $passedStates);
    }

    public function hasBeenInAnyStatus($statusCodes = [])
    {
        $history = $this->getStatusHistory();
        if (is_null($history)) {
            return false;
        }
        $passedStates = Arr::pluck($history, 'status_code', 'position');
        return count(array_intersect($statusCodes, $passedStates)) > 0;
    }

    public function isCurrentlyInGroup($group)
    {
        return $this->fsm->isInGroup($this->status,$group);
    }

    public function isInDraftState() {
        return $this->isCurrentlyInGroup('draft');
    }

    public function getLastStatusDescription() {
        return $this->fsm->getStateDescription($this->status);
    }

    public function scopeIsInStateGroup($query, $group) {
        return $query->whereIn($this->getStatusFieldname(),$this->getFSM()->getAllCodesInGroup($group));
    }

    public function scopeIsNotInStateGroup($query, $group) {
        return $query->whereNotIn($this->getStatusFieldname(),$this->getFSM()->getAllCodesInGroup($group));
    }
    public function scopeDraftable($query) {
        return $query->isInStateGroup('draft');
    }

    public function scopeNotDraftable($query) {
        return $query->isNotInStateGroup('draft');
    }
}
