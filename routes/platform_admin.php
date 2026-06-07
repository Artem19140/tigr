<?php

use App\Enums\EmployeeRole;
use App\Http\Controllers\Web\Center\CenterController;
use App\Http\Controllers\Web\Center\CenterPlatformAdminController;
use App\Http\Controllers\Web\PlatformAdmin\CommandsController;
use App\Http\Controllers\Web\PlatformAdmin\LogsController;
use App\Http\Controllers\Web\PlatformAdmin\PlatformAdminStatisticsController;
use App\Support\AppMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware([
        AppMiddleware::LOG_CONTEXT,
        AppMiddleware::REQUEST_TIME_MEASURE,
        AppMiddleware::EMPLOYEE_HAS_ANY_ROLE.':'.EmployeeRole::PlatformAdmin->value,
    ])
    ->group(function () {
        Route::get('centers', [CenterPlatformAdminController::class, 'index']);
        //Route::get('centers/{center}', [CenterController::class, 'show']);
        Route::post('centers', [CenterPlatformAdminController::class, 'store']);
        Route::delete('centers', [CenterPlatformAdminController::class, 'destroy']);

        Route::get('statistics', [PlatformAdminStatisticsController::class, 'index']);

        Route::inertia('logs', 'PlatformAdmin/Logs')->name('platform-admin.logs');
        Route::inertia('commands', 'PlatformAdmin/Commands')->name('platform-admin.commands');
        Route::inertia('home', 'PlatformAdmin/Home')->name('platform-admin.home');

        Route::post('commands', [CommandsController::class, 'execute']);

        Route::get('logs/download', [LogsController::class, 'download'])->name('logs.download');
        Route::get('logs/available', [LogsController::class, 'available']);
    });
