<?php

use App\Http\Controllers\Web\PlatformManage\CommandsController;
use App\Http\Controllers\Web\PlatformManage\LogsController;
use App\Modules\Shared\ExamSettings;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('/admin')
    ->middleware([
        'meta',
        'auth:web',
        'can:platform-manage',
    ])
    ->group(function () {

        Route::get('logs', function(){
            return Inertia::render('PlatformAdmin/Logs', [
                'attemptMinDurationMin' => ExamSettings::attemptMinDurationMinutes(),
                'examMinTimeBefore' =>  ExamSettings::minTimeBeforeCreateMinutes(),
                'enrollmentMinTimeBefore' =>  ExamSettings::enrollmentCloseBeforeExamMinutes(),
            ]);
        })
            ->name('platform-admin.logs');
        Route::inertia('commands', 'PlatformAdmin/Commands')->name('platform-admin.commands');

        Route::post('commands', [CommandsController::class, 'execute']);

        Route::get('logs/download', [LogsController::class, 'download'])->name('logs.download');

        Route::get('logs/available', [LogsController::class, 'available']);

        Route::get('logs/git', [LogsController::class, 'downloadGitLog']);
    });