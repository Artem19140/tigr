<?php

use App\Http\Controllers\Web\Report\ReportController;
use Illuminate\Http\Request;
use Inertia\Inertia;

Route::prefix('reports')->group(function () {
    Route::get('',[ReportController::class, 'resolve'])
        ->can('reports.viewAny')
        ->name('reports.resolver');

    Route::get('frdo/download', [ReportController::class, 'frdo'])
        ->can('reports.frdo')
        ->name('reports.frdo.download');

    Route::get('frdo/available', [ReportController::class, 'availableFrdo'])
        ->can('reports.frdo')
        ->name('reports.frdo.available');

    Route::get('flat-table/download', [ReportController::class, 'flatTable'])
        ->can('reports.flat-table')
        ->name('reports.flat-table.download');

    Route::get('ministry-education/available', [ReportController::class, 'availableMinistryEducation'])
        ->can('reports.ministry-education')
        ->name('reports.ministry-education.available');

    Route::get('ministry-education/download', [ReportController::class, 'ministryEducation'])
        ->can('reports.ministry-education')
        ->name('reports.ministry-education.download');
    //1
    Route::get('frdo', function(Request $request){
        $employee = $request->user();
        return  Inertia::render('Report/Frdo', [
            'permissions' => [
                'flatTable'  => $employee->can('reports.flat-table'),
                'frdo' =>$employee->can('reports.frdo'),
                'ministryEducation' =>$employee->can('reports.ministry-education'),
            ]
        ]);
    })
        ->can('reports.frdo')
        ->name('reports.frdo');
    //2
    Route::get('ministry-education', function(Request $request){
        $employee = $request->user();
        return  Inertia::render('Report/MinistryEducation', [
            'permissions' => [
                'flatTable'  => $employee->can('reports.flat-table'),
                'frdo' =>$employee->can('reports.frdo'),
                'ministryEducation' =>$employee->can('reports.ministry-education'),
            ]
        ]);
    })
        ->can('reports.ministry-education')
        ->name('reports.ministry-education');
    //3
    Route::get('flat-table', function(Request $request){
        $employee = $request->user();
        return  Inertia::render('Report/FlatTable', [
            'permissions' => [
                'flatTable'  => $employee->can('reports.flat-table'),
                'frdo' =>$employee->can('reports.frdo'),
                'ministryEducation' =>$employee->can('reports.ministry-education'),
            ]
        ]);
    })
        ->can('reports.flat-table')
        ->name('reports.flat-table');
});