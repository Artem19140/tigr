<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

class ObservabilityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        DB::listen(function ($query) {
            if ($query->time > 300) {
                Log::warning('Slow query', [
                    'sql' => $query->sql,
                    'time' => $query->time,
                ]);
            }
        });

        $this->app[HttpKernel::class]->whenRequestLifecycleIsLongerThan(
            1000,
            function ($startedAt, $request, $response) {
                Log::warning("A request took longer than 1 seconds.");
            }
        );
    }
}
