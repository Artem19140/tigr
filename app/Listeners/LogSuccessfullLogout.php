<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogSuccessfullLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        activity()
            ->causedBy($event->user)
            ->event('security')
            ->log("$event->guard:logout");
    }
}
