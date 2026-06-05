<?php

use App\Enums\EmployeeRole;
use App\Http\Controllers\Web\Center\CenterSuperAdminController;
use App\Http\Controllers\Web\SuperAdmin\CommandsController;
use App\Http\Controllers\Web\SuperAdmin\LogsController;
use App\Http\Controllers\Web\SuperAdmin\SuperAdminStatisticsController;
use App\Support\AppMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware([
        AppMiddleware::LOG_CONTEXT,
        AppMiddleware::REQUEST_TIME_MEASURE,
        AppMiddleware::EMPLOYEE_HAS_ANY_ROLE.':'.EmployeeRole::SuperAdmin->value,
    ])
    ->group(function () {
        Route::get('centers', [CenterSuperAdminController::class, 'index']);
        Route::get('centers/{center}', [CenterSuperAdminController::class, 'show']);
        Route::post('centers', [CenterSuperAdminController::class, 'store']);
        Route::delete('centers', [CenterSuperAdminController::class, 'destroy']);

        Route::get('statistics', [SuperAdminStatisticsController::class, 'index']);

        Route::inertia('logs', 'SuperAdmin/Logs')->name('super-admin.logs');
        Route::inertia('commands', 'SuperAdmin/Commands')->name('super-admin.commands');
        Route::inertia('home', 'SuperAdmin/Home')->name('super-admin.home');

        Route::post('commands', [CommandsController::class, 'execute']);

        Route::get('logs/download', [LogsController::class, 'download'])->name('logs.download');
        Route::get('logs/available', [LogsController::class, 'available']);
    });
