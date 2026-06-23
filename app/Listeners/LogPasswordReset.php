<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use App\Support\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogPasswordReset
{
    public function __construct(
        protected Audit $audit
    ){}

    public function handle(PasswordReset $event): void
    {
        $this->audit->log(
            'password_reset', 
            $event->user 
        );
    }
}
