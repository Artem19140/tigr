<?php

use App\Exceptions\BusinessException;
use App\Http\Middleware\EnsureEmployeeActive;
use App\Http\Middleware\EnsureValidAttemptStatus;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LogContext;
use App\Http\Middleware\RequestTimeMeasure;
use App\Modules\Shared\CodeTranslator;
use App\Support\AppMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'employee.active' => EnsureEmployeeActive::class,
            AppMiddleware::ENSURE_ATTEMPT_VALID_STATUS => EnsureValidAttemptStatus::class,
        ]);

        $middleware->appendToGroup('meta', [
            LogContext::class,
            RequestTimeMeasure::class
        ]);

        $middleware->trustProxies(
            at: ['10.0.0.0/8']
        );

        $middleware->redirectUsersTo('/me');
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            BusinessException::class,
        ]);

        $exceptions->render(function (BusinessException $e, Request $request) {
            $translator = new CodeTranslator();

            $message = $translator->translate(
                $e->reasonCode, 
                $e->params
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'code' => $e->reasonCode
                ], 400);
            }

            return Inertia::flash([
                'error' => $message,
                'code' => $e->reasonCode
            ])->back();
        });
    })->create();
