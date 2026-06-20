<?php

use App\Http\Controllers\Web\PlatformAdmin\CenterPlatformAdminController;
use App\Http\Controllers\Web\PlatformAdmin\CommandsController;
use App\Http\Controllers\Web\PlatformAdmin\LogsController;
use App\Http\Controllers\Web\PlatformAdmin\PlatformAdminStatisticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware([
        'meta',
        'can:platform-manage',
    ])
    ->group(function () {
        Route::get('centers', [CenterPlatformAdminController::class, 'index']);
        Route::post('centers', [CenterPlatformAdminController::class, 'store']);
        Route::delete('centers/{center}', [CenterPlatformAdminController::class, 'destroy']);

        Route::put('centers/id', [CenterPlatformAdminController::class, 'change']);
        Route::delete('centers/id', [CenterPlatformAdminController::class, 'reset']);

        Route::get('statistics', [PlatformAdminStatisticsController::class, 'index']);

        Route::inertia('logs', 'PlatformAdmin/Logs')->name('platform-admin.logs');
        Route::inertia('commands', 'PlatformAdmin/Commands')->name('platform-admin.commands');
        Route::inertia('home', 'PlatformAdmin/Home')->name('platform-admin.home');

        Route::post('commands', [CommandsController::class, 'execute']);

        Route::get('logs/download', [LogsController::class, 'download'])->name('logs.download');

        Route::get('logs/available', [LogsController::class, 'available']);

        Route::get('logs/git', [LogsController::class, 'downloadGitLog']);
    });
