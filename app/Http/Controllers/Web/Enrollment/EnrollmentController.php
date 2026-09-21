<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Modules\Enrollment\ChangePaymentStatus;
use App\Modules\Enrollment\CreateEnrollment;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Models\Enrollment;
use App\Modules\Shared\CenterData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;

class EnrollmentController
{
    public function store(
        EnrollmentStoreRequest $request,
        CreateEnrollment $createEnrollment,
    ): JsonResponse {
        $enrollment = $createEnrollment->execute(
            $request->validated('examId'),
            $request->validated('foreignNationalId'),
            $request->user(),
            $request->validated('hasPayment')
        );

        return response()->json([
            'redirectUrl' => route('enrollments.statements', [
                'enrollment' => $enrollment,
            ]),
        ]);
    }

    public function changePayment(
        Enrollment $enrollment,
        ChangePaymentStatus $changePaymentStatus
    ): RedirectResponse {
        $changePaymentStatus->execute($enrollment);

        return Inertia::back();
    }

    public function statement(
        Enrollment $enrollment,
    ): Response {
        
        $enrollment->load([
            'foreignNational',
            'exam.type',
            'creator'
        ]);

        $statementPdf = Pdf::loadView('pdf.enrollment.enrollment-full', [
            'enrollment' => $enrollment,
            'center' => new CenterData()
        ]);

        $fileName = "Заявление_согласие_{$enrollment->foreignNational->full_name}.pdf";
        
        return $statementPdf->stream($fileName);
    }
}
