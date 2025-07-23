<?php

namespace App\ProposalStatusActivities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class StatusActivity implements StatusActivityInterface, Arrayable, \JsonSerializable
{

    public static $type = 'generic';

    protected $data = [];

    protected $timestamp = null;

    protected $statusKey = null;

    protected $model;

    /**
     * @param array $data
     */
    public function __construct($data = [], $timestamp = null, Model $model = null, $statusKey = null)
    {
        $this->model = $model;
        $this->data = $data;
        $this->timestamp = $timestamp ?: Carbon::now()->toDateTimeString();
        $this->statusKey = $statusKey;
    }

    public function getType() {
        return static::$type;
    }

    public function getName() {
        $name = Lang::get('status_activities.types.'.static::$type);
        return $name == 'status_activities.types.'.static::$type ? static::$type : $name;
    }
    public function getStatusKey() {
        return $this->statusKey;
    }

    public function setStatusKey($statusKey) {
        return $this->statusKey = $statusKey;
    }

    /**
     * @return mixed|string|null
     */
    public function getTimestamp(): mixed
    {
        return $this->timestamp;
    }

    /**
     * @param mixed|string|null $timestamp
     */
    public function setTimestamp(mixed $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * @param Model|null $model
     */
    public function setModel(?Model $model): void
    {
        $this->model = $model;
    }


    public function getViewData(User $user = null, $viewType = null) {

        if (is_null($user)) {
            $user = Auth::user();
        }

        $buildViewMethod = $viewType
            ? 'buildViewData'.Str::studly($viewType)
            : 'buildViewData';

        if (method_exists($this,$buildViewMethod)) {
            return $this->$buildViewMethod($user);
        }

        return $this->toArray();

    }

    public function toArray() {
        return [
            'type' => $this->getType(),
            'timestamp' => $this->timestamp,
            'data' => $this->data,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
        // TODO: Implement jsonSerialize() method.
    }


}
