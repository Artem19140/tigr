<?php

use App\Enums\CounterKey;
use App\Http\Controllers\Web\Enrollment\EnrollmentController;
use App\Models\Counter;
use App\Models\Enrollment;
use App\Support\Audit;
use App\Support\ModelAuditChangesBuilder;
use Illuminate\Http\Request;


Route::post('/enrollments', [EnrollmentController::class, 'store'])
    ->name('enrollments.store')
    ->can('create', Enrollment::class);

Route::put('enrollments/{enrollment}/payment', [EnrollmentController::class, 'changePayment'])
    ->middleware('can:payment,enrollment')
    ->name('enrollments.payment-change');

Route::get('enrollments/{enrollment}/full-documents', [EnrollmentController::class, 'fullDocuments'])
    ->middleware('can:statement,enrollment')
    ->name('enrollments.full-documents');

Route::get('enrollments/{enrollment}/statements', [EnrollmentController::class, 'statement'])
    ->middleware('can:statement,enrollment')
    ->name('enrollments.statements');

Route::get('enrollments/{enrollment}/approvals', [EnrollmentController::class, 'approval'])
    ->middleware('can:statement,enrollment')
    ->name('enrollments.approvals');


Route::delete('exams/{exam}/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])
    ->scopeBindings()
    ->name('enrollments.destroy')
    ->can('delete', Enrollment::class);

Route::put('enrollments/{enrollment}/reg-number', function(
    Enrollment $enrollment, 
    Request $request,
    Audit $audit,
    ModelAuditChangesBuilder $builder
){
    $request->validate([
        'newRegNumber' => [
            'required', 
            'integer', 
            'regex:' . Counter::firstWhere(['key' => CounterKey::RegNum])->regexValidaton()
        ]
    ]);

    $enrollment->update([
        'reg_number' => $request->input('newRegNumber')
    ]);

    $audit->log(
        'update',
        $enrollment,
        [
            'changes' => $builder->build($enrollment)
        ]
    );

    return response()->noContent();
})->name('enrollments.update.reg-number');
