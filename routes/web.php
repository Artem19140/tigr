<?php

use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\Enrollment\EnrollmentController;
use App\Http\Controllers\Web\Enrollment\EnrollmentDocumentController;
use App\Http\Controllers\Web\Document\DocumentController;
use App\Http\Controllers\Web\ForeignNational\ForeignNationalController;
use App\Http\Controllers\Web\ForeignNational\ForeignNationalExportController;
use App\Http\Controllers\Web\Report\ReportController;
use App\Http\Controllers\Web\Statistics\StatisticsController;
use App\Http\Controllers\Web\Upload\UploadController;
use App\Http\RedirectResolver;
use App\Models\Enrollment;
use App\Models\ForeignNational;
use App\Support\AppMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'meta',
    'auth',
    AppMiddleware::EMPLOYEE_ACTIVE,
    AppMiddleware::CENTER_ACTIVE
])
    ->group(function () {
        Route::inertia('foreign-nationals/create', 'ForeignNationals/ForeignNationalCreate');
        Route::apiResource('foreign-nationals', ForeignNationalController::class)
            ->except('delete')
            ->where(['foreign_national' => '[0-9]+']);

        Route::post('/enrollments', [EnrollmentController::class, 'store'])
            ->can('create', Enrollment::class);

        Route::put('enrollments/{enrollment}/payment', [EnrollmentController::class, 'changePayment'])
            ->middleware('can:payment,enrollment')
            ->name('enrollments.change.payment');

        Route::get('enrollments/{enrollment}/statements', [EnrollmentDocumentController::class, 'statement'])
            ->middleware('can:statement,enrollment')
            ->name('enrollments.statements');

        Route::get('foreign-nationals/export', [ForeignNationalExportController::class, 'export'])
            ->can('export', ForeignNational::class)
            ->name('foreign-nationals.export');

        Route::get('foreign-nationals/export/available', [ForeignNationalExportController::class, 'exportAvailable'])
            ->can('export', ForeignNational::class);
            
        Route::get('statistics', [StatisticsController::class, 'index'])
            ->can('statistics');

        Route::prefix('reports')->group(function () {
            Route::get('frdo', [ReportController::class, 'frdo'])
                ->can('reports.frdo')
                ->name('reports.frdo');

            Route::get('frdo/available', [ReportController::class, 'availableFrdo'])
                ->can('reports.frdo')
                ->name('reports.frdo.available');

            Route::get('flat-table', [ReportController::class, 'flatTable'])
                ->can('reports.flat-table')
                ->name('reports.flat-table');

            Route::get('ministry-education/available', [ReportController::class, 'availableMinistryEducationReport'])
                ->can('reports.min-education')
                ->name('reports.ministry-education.available');

            Route::get('ministry-education', [ReportController::class, 'ministryEducationReport'])
                ->can('reports.min-education')
                ->name('reports.ministry-education');
        });

        require __DIR__.'/center_manage.php';

        require __DIR__.'/exams.php';

        require __DIR__.'/attempts_manage.php';


        Route::get('documents/{document}', [DocumentController::class, 'show']);

        Route::put('documents/{document}', [DocumentController::class, 'update'])
            ->can('update','document');

        Route::post('uploads',  [UploadController::class, 'store'])
            ->name('uploads.store');
        
        Route::post('uploads/{upload}/chunks',  [UploadController::class, 'chunk'])
            ->name('uploads.store.chunks');

         Route::post('documents/link',  [DocumentController::class, 'link'])
            ->name('documents.link');

        Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
        Route::post('logout/all', [LogoutController::class, 'logoutAll'])->name('logout.all');
    });

require __DIR__.'/auth.php';
require __DIR__.'/attempts_passing.php';
require __DIR__.'/platform_manage.php';

Route::get('/', function(){
    return redirect('login');
});

Route::middleware([
    'meta',
    'auth:web,foreignNationals'
])->get('me', function (RedirectResolver $resolver) {
    return redirect($resolver->execute());
})->name('me');