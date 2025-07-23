<?php

namespace App\Observers;

use App\Models\Method;

class MethodObserver
{
    /**
     * Handle the Method "created" event.
     */
    public function created(Method $method): void
    {
        //
    }

    /**
     * Handle the Method "updated" event.
     */
    public function updated(Method $method): void
    {
        foreach($method->services as $service){
            // Tells scout to reindex the service in elastic
            $service->searchable();
        }
    }

    /**
     * Handle the Method "deleted" event.
     */
    public function deleted(Method $method): void
    {
        //
    }

    /**
     * Handle the Method "restored" event.
     */
    public function restored(Method $method): void
    {
        //
    }

    /**
     * Handle the Method "force deleted" event.
     */
    public function forceDeleted(Method $method): void
    {
        //
    }
}
