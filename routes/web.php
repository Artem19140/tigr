<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\Auth\PasswordController;
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
use App\Models\Exam;
use App\Models\ForeignNational;
use App\Support\AppMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([
    'meta',
    'auth',
    AppMiddleware::EMPLOYEE_ACTIVE,
    AppMiddleware::CENTER_ACTIVE
])
    ->group(function () {
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

        Route::prefix('instruction')->group(function () {
            Route::inertia('/exams', 'Instruction/ExamsInstruction')
                ->can('viewAny', Exam::class)
                ->name('instruction.exams');

            Route::inertia('/foreign-nationals', 'Instruction/ForeignNationalsInstruction')
                ->name('instruction.foreign-nationals')
                ->can('viewAny', ForeignNational::class);

            Route::inertia('/exams/monitoring', 'Instruction/ExamMonitoringInstruction')
                ->name('instruction.exams.monitoring')
                ->can('monitoringAny', Exam::class);

            Route::inertia('/exams/checking', 'Instruction/ExamCheckingInstruction')
                ->name('instruction.exams.checking')
                ->can('checkingAny', Exam::class);

            Route::inertia('/exams/schedule', 'Instruction/ExamScheduleInstruction')
                ->can('viewAny', Exam::class)
                ->name('instruction.exams.schedule');
        });

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

Route::middleware([
    'meta',
    'guest:web,foreignNationals'
])->group(function () {
    Route::inertia('login', 'Auth/Login')
        ->name('login');

    Route::post('login', [LoginController::class, 'login'])
        ->middleware(['throttle:5']);

    Route::get('/reset-password/{token}', fn ($token) => Inertia::render('Auth/ChangePassword', [
        'token' => $token,
        'email' => request()->query('email')
    ]))->name('password.reset');

    Route::get('/forgot-password', fn () => 
        Inertia::render('Auth/ForgotPassword', [])
    )->name('password.forgot');

    Route::post('/forgot-password', [PasswordController::class, 'forgot'])->name('password.email');

    Route::post('password/reset', [PasswordController::class, 'change']);
});

Route::get('/', function(){
    return redirect('login');
});

Route::middleware([
    'meta',
    'auth:web,foreignNationals'
])->get('me', function (RedirectResolver $resolver) {
    return redirect($resolver->execute());
})->name('me');

require __DIR__.'/attempts_passing.php';
require __DIR__.'/platform_manage.php';