<?php

use App\Http\Controllers\Web\Report\ReportController;
use Inertia\Inertia;

Route::prefix('reports')->group(function () {
    
    Route::get('resolver',[ReportController::class, 'resolve'])
        ->can('reports.viewAny')
        ->name('reports.resolver');

    Route::get('frdo/download', [ReportController::class, 'frdo'])
        ->can('reports.frdo')
        ->name('reports.frdo.download');

    Route::get('frdo/availability', [ReportController::class, 'availabilityFrdo'])
        ->can('reports.frdo')
        ->name('reports.frdo.availability');

    Route::get('flat-table/download', [ReportController::class, 'flatTable'])
        ->can('reports.flat-table')
        ->name('reports.flat-table.download');

    Route::get('ministry-education/available', [ReportController::class, 'availableMinistryEducation'])
        ->can('reports.ministry-education')
        ->name('reports.ministry-education.availability');

    Route::get('ministry-education/download', [ReportController::class, 'ministryEducation'])
        ->can('reports.ministry-education')
        ->name('reports.ministry-education.download');

    Route::get('frdo', function(){
        return  Inertia::render('Report/Frdo', [
            'availabilityUrl' => route('reports.frdo.availability')
        ]);
    })
        ->can('reports.frdo')
        ->name('reports.frdo');

    Route::get('ministry-education', function(){
        return  Inertia::render('Report/MinistryEducation', [
            'availabilityUrl' => route('reports.ministry-education.availability')
        ]);
    })
        ->can('reports.ministry-education')
        ->name('reports.ministry-education');

    Route::get('flat-table', function(){
        return  Inertia::render('Report/FlatTable', [
            'downloadUrl' => route('reports.flat-table.download')
        ]);
    })
        ->can('reports.flat-table')
        ->name('reports.flat-table');
});