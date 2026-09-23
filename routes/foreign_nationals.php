<?php

use App\Http\Controllers\Web\ForeignNational\ForeignNationalController;
use App\Http\Controllers\Web\ForeignNational\ForeignNationalDocumentsController;


Route::resource('foreign-nationals', ForeignNationalController::class)
    ->except('delete')
    ->where(['foreign_national' => '[0-9]+']);

Route::get(
    'foreign-nationals/documents/{foreign_national_document}', 
    [ForeignNationalDocumentsController::class, 'show']
)
    ->scopeBindings()
    ->can('view', 'foreign_national_document')
    ->name('foreign-nationals.documents.show');

Route::put(
    'foreign-nationals/documents/{foreign_national_document}', 
    [ForeignNationalDocumentsController::class, 'update']
)
    ->scopeBindings()
    ->can('update', 'foreign_national_document')
    ->name('foreign-nationals.documents.update');
