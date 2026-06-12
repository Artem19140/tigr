<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Exam;
use App\Models\ForeignNational;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'foreign_national' => ForeignNational::class,
            'employee' => Employee::class,
            'exam' => Exam::class
        ]);

        Model::shouldBeStrict(! app()->isProduction());

        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            if (\in_array($response->statusCode(), [403, 404, 500, 503])) {
                return $response->render('ErrorPage', [
                    'status' => $response->statusCode(),
                    'message' => $response->exception->getMessage()
                ])->withSharedData();
            }
        });
    }
}
