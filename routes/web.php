<?php

use App\Http\Controllers\Web\Enrollment\EnrollmentController;
use App\Http\Controllers\Web\Document\DocumentController;
use App\Http\Controllers\Web\ForeignNational\ForeignNationalController;
use App\Http\Controllers\Web\ForeignNational\ForeignNationalExportController;
use App\Http\RedirectResolver;
use App\Models\Enrollment;
use App\Models\ForeignNational;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'meta',
    'auth',
    'employee.active'
])
    ->group(function () {
        Route::resource('foreign-nationals', ForeignNationalController::class)
            ->except('delete')
            ->where(['foreign_national' => '[0-9]+']);

        Route::post('/enrollments', [EnrollmentController::class, 'store'])
            ->can('create', Enrollment::class);

        Route::put('enrollments/{enrollment}/payment', [EnrollmentController::class, 'changePayment'])
            ->middleware('can:payment,enrollment')
            ->name('enrollments.payment-change');

        Route::get('enrollments/{enrollment}/statements', [EnrollmentController::class, 'statement'])
            ->middleware('can:statement,enrollment')
            ->name('enrollments.statements');

        Route::get('foreign-nationals/export', [ForeignNationalExportController::class, 'export'])
            ->can('export', ForeignNational::class)
            ->name('foreign-nationals.export');

        Route::get('foreign-nationals/export/available', [ForeignNationalExportController::class, 'exportAvailable'])
            ->can('export', ForeignNational::class);

        require __DIR__.'/reports.php';

        require __DIR__.'/center_management.php';

        require __DIR__.'/exams.php';

        require __DIR__.'/exams_review.php';

        require __DIR__.'/exams_conduct.php';


        Route::get('documents/{document}', [DocumentController::class, 'show']);

        Route::put('documents/{document}', [DocumentController::class, 'update'])
            ->can('update','document');

        
    });

require __DIR__.'/auth.php';
require __DIR__.'/exams_session.php';
require __DIR__.'/platform_management.php';

Route::get('/', function(){
    return redirect('login');
});

Route::middleware([
    'meta',
    'auth:web,foreignNationals'
])->get('me', function (RedirectResolver $resolver) {
    return redirect($resolver->execute());
})->name('me');