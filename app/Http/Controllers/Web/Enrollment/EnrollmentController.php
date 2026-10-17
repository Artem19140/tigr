<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Modules\Enrollment\ChangePaymentStatus;
use App\Modules\Enrollment\CreateEnrollment;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Models\Enrollment;
use App\Modules\Shared\CenterData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        Request $request,
        Enrollment $enrollment,
        ChangePaymentStatus $changePaymentStatus
    ): Response {
        $request->validate([
            'status' => ['required', 'boolean']
        ]);

        $changePaymentStatus->execute(
            $enrollment,
            $request->boolean('status')
        );
        return response()->noContent();
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

        $fullName = $enrollment->foreignNational->surname . '_' . $enrollment->foreignNational->name . '_' . $enrollment->foreignNational->patronymic;

        $fileName = "Заявление_согласие_{$fullName}.pdf";
        
        return $statementPdf->stream($fileName);
    }
}
