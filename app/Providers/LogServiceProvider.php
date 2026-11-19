<?php

namespace App\Providers;

use App\Models\Employee;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\LogFile;
use Opcodes\LogViewer\LogFolder;
use Spatie\Activitylog\Facades\Activity;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('viewLogViewer', function (Employee $actor) {
            return $actor->isPlatformAdmin() 
                ?   true
                :   Response::denyAsNotFound();
        });

        Gate::define('downloadLogFolder', function (Employee $actor, LogFolder $folder) {
            return $actor->isPlatformAdmin() 
                ?   true
                :   Response::denyAsNotFound();
        });

        Gate::define('downloadLogFile', function (Employee $actor, LogFile $file) {
            return $actor->isPlatformAdmin() 
                ?   true
                :   Response::denyAsNotFound();
        });

        Gate::define('deleteLogFile', function (Employee $actor, LogFile $file) {
            return $actor->isPlatformAdmin() 
                ?   true
                :   Response::denyAsNotFound();
        });

        Gate::define('deleteLogFolder', function (Employee $actor, LogFolder $folder) {
            return $actor->isPlatformAdmin() 
                ?   true
                :   Response::denyAsNotFound();
        });

        Activity::beforeLogging(function ($activity) {
            $activity->properties = $activity->properties->put('ip', context('ip'));
        });

        Activity::beforeLogging(function ($activity) {
            $activity->properties = $activity->properties->put('client_ip', context('client_ip'));
        });

        Activity::beforeLogging(function ($activity) {
            $activity->properties = $activity->properties->put('user_agent', context('user_agent'));
        });

        Activity::beforeLogging(function ($activity) {
            $activity->properties = $activity->properties->put('url', context('url'));
        });

        Activity::beforeLogging(function ($activity) {
            $activity->properties = $activity->properties->put('request_id', context('request_id'));
        });
    }
}
