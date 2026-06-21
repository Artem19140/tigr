<?php

namespace App\Listeners;

use App\Support\Audit;
use Illuminate\Auth\Events\Logout;

class LogSuccessfullLogout
{
    /**
     * Create the event listener.
     */
    public function __construct(protected Audit $audit)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $this->audit->log(
            "$event->guard:logout", 
            $event->user
        );
    }
}
