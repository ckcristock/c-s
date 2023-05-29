<?php

namespace App\Observers;

use App\Events\NewNotification;
use App\Models\Alert;

class AlertObserver
{
    /**
     * Handle the Alert "created" event.
     *
     * @param  \App\Models\Alert  $alert
     * @return void
     */
    public function created(Alert $alert)
    {
        if ($alert->user_id == auth()->user()->person_id) {
            event(new NewNotification($alert));
        }
    }

    /**
     * Handle the Alert "updated" event.
     *
     * @param  \App\Models\Alert  $alert
     * @return void
     */
    public function updated(Alert $alert)
    {
        //
    }

    /**
     * Handle the Alert "deleted" event.
     *
     * @param  \App\Models\Alert  $alert
     * @return void
     */
    public function deleted(Alert $alert)
    {
        //
    }

    /**
     * Handle the Alert "restored" event.
     *
     * @param  \App\Models\Alert  $alert
     * @return void
     */
    public function restored(Alert $alert)
    {
        //
    }

    /**
     * Handle the Alert "force deleted" event.
     *
     * @param  \App\Models\Alert  $alert
     * @return void
     */
    public function forceDeleted(Alert $alert)
    {
        //
    }
}
