<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;

class LogPasswordReset
{
    public function __construct(){}

    public function handle(PasswordReset $event): void
    {
        activity()
            ->event('security')
            ->causedBy($event->user)
            ->log('password_reset');
    }
}
