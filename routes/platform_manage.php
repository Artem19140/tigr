<?php

use App\Http\Controllers\Web\CenterManage\CenterController;
use App\Http\Controllers\Web\PlatformManage\CommandsController;
use App\Http\Controllers\Web\PlatformManage\LogsController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware([
        'meta',
        'auth:web',
        'can:platform-manage',
    ])
    ->group(function () {
        Route::get('centers', [CenterController::class, 'index'])->name('centers.index');
        Route::post('centers', [CenterController::class, 'store']);
        Route::delete('centers/{center}', [CenterController::class, 'destroy']);

        Route::inertia('logs', 'PlatformAdmin/Logs')->name('platform-admin.logs');
        Route::inertia('commands', 'PlatformAdmin/Commands')->name('platform-admin.commands');

        Route::post('commands', [CommandsController::class, 'execute']);

        Route::get('logs/download', [LogsController::class, 'download'])->name('logs.download');

        Route::get('logs/available', [LogsController::class, 'available']);

        Route::get('logs/git', [LogsController::class, 'downloadGitLog']);
    });