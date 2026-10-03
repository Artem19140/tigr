<?php

use App\Http\Controllers\Web\Report\FlatTableController;
use App\Http\Controllers\Web\Report\FrdoController;
use App\Http\Controllers\Web\Report\MinistryEducationController;
use Inertia\Inertia;

Route::prefix('reports')->group(function () {

    Route::get('frdo/download', [FrdoController::class, 'download'])
        ->can('reports.frdo')
        ->name('reports.frdo.download');

    Route::get('frdo/availability', [FrdoController::class, 'availability'])
        ->can('reports.frdo')
        ->name('reports.frdo.availability');

    Route::get('flat-table/download', [FlatTableController::class, 'download'])
        ->can('reports.flat-table')
        ->name('reports.flat-table.download');

    Route::get('flat-table/availability', [FlatTableController::class, 'availability'])
        ->can('reports.flat-table')
        ->name('reports.flat-table.availability');

    Route::get('ministry-education/availability', [MinistryEducationController::class, 'availability'])
        ->can('reports.ministry-education')
        ->name('reports.ministry-education.availability');

    Route::get('ministry-education/download', [MinistryEducationController::class, 'download'])
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
            'availabilityUrl' => route('reports.flat-table.availability')
        ]);
    })
        ->can('reports.flat-table')
        ->name('reports.flat-table');
});