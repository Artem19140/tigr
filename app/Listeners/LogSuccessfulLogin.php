<?php

namespace App\Listeners;

use App\Support\Audit;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct(protected Audit $audit)
    {
        //
    }

    public function handle(Login $event): void
    {
        $this->audit->log(
            "$event->guard:login", 
            $event->user, 
            [
                'remember' => $event->remember,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => $event->user->id
            ]
        );
    }
}
