<?php

namespace App\Observers;


use Illuminate\Database\Eloquent\Model;

class CordraObserver
{
    public function saved(Model $model): void
    {
        $model->setToSync();
    }
}
