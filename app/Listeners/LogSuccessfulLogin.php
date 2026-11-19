<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct()
    {
        //
    }

    public function handle(Login $event): void
    {
        activity()
            ->event('security')
            ->causedBy($event->user)
            ->withProperties(
                [
                    'remember' => $event->remember,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            )
            ->log("$event->guard:login");
    }
}
