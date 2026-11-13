<?php

use App\Http\Controllers\Web\ForeignNational\ForeignNationalController;
use App\Http\Controllers\Web\ForeignNational\ForeignNationalDocumentsController;
use App\Http\Requests\ForeignNational\ForeignNationalIndexRequest;
use App\Modules\ForeignNational\GetForeignNationals;
use Illuminate\Http\Request;


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

Route::get('foreign-nationals/matches', function(
    ForeignNationalIndexRequest $request,
    GetForeignNationals $getForeignNationals
){
    $matches = $getForeignNationals->execute(
        $request->toDto()
    )
        ->limit(10)
        ->get()
        ->map(function($match){
            return [
                'id' => $match->id,
                'fullName' => $match->full_name,
                'passport'=> $match->formattedDateBirth(),
                'showUrl' => route('foreign-nationals.show', [
                    'foreign_national' => $match->id
                ], false)
            ];
        });

    return response()->json([
        'matches' => $matches
    ]);
})->name('foreign-nationals.matches');