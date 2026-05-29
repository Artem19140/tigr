<?php

use App\Enums\EmployeeRole;
use App\Http\Controllers\Web\Center\CenterSuperAdminController;
use App\Http\Controllers\Web\SuperAdmin\LogsController;
use App\Http\Controllers\Web\SuperAdmin\SuperAdminStatisticsController;
use App\Support\AppMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware(['request.time.measure', AppMiddleware::EMPLOYEE_HAS_ANY_ROLE.':'.EmployeeRole::SuperAdmin->value])
    ->group(function () {
        Route::inertia('home', 'SuperAdmin/Home')->name('super-admin.home');
        
        Route::get('centers', [CenterSuperAdminController::class, 'index']);
        Route::get('centers/{center}', [CenterSuperAdminController::class, 'show']);
        Route::post('centers', [CenterSuperAdminController::class, 'store']);
        Route::delete('centers', [CenterSuperAdminController::class, 'destroy']);

        Route::get('statistics', [SuperAdminStatisticsController::class, 'index']);

        Route::inertia('logs', 'SuperAdmin/Logs')->name('super-admin.logs');

        
        Route::get('logs/download', [LogsController::class, 'download'])->name('logs.download');

        Route::get('logs/available', [LogsController::class, 'available']);
    });
