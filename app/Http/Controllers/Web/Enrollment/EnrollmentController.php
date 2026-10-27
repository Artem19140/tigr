<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\Enrollment\ChangePaymentStatus;
use App\Modules\Enrollment\CreateEnrollment;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Models\Enrollment;
use App\Modules\Enrollment\EnrollmentCancelledRules;
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
            'redirectUrl' => route('enrollments.full-documents', [
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

    public function fullDocuments(
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

    public function statement(
        Enrollment $enrollment
    ): Response {
        $enrollment->load([
            'foreignNational',
            'exam.type',
            'creator'
        ]);

        $statementPdf = Pdf::loadView('pdf.enrollment.statement', [
            'enrollment' => $enrollment,
            'center' => new CenterData()
        ]);

         $fileName = "Заявление_{$enrollment->foreignNational->full_name}.pdf";
        
        return $statementPdf->stream($fileName);
    }

    public function approval(
        Enrollment $enrollment
    ): Response {
        $enrollment->load([
            'foreignNational',
            'exam.type',
            'creator'
        ]);

        $statementPdf = Pdf::loadView('pdf.enrollment.approval', [
            'enrollment' => $enrollment,
            'center' => new CenterData()
        ]);

        $fileName = "Согласие_{$enrollment->foreignNational->full_name}.pdf";
        
        return $statementPdf->stream($fileName);
    }
    

    public function destroy(
        Request $request,
        Exam $exam,
        Enrollment $enrollment,
        EnrollmentCancelledRules $rules
    ): Response{

        $result = $rules->check(
            $enrollment,
            $exam
        );

        if($result->isNotAvailable()){
            throw new BusinessException(
                $result->code()
            );
        }

        $enrollment->update([
            'cancelled_at' => now()
        ]);
        return response()->noContent();
    }
}
