<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Enums\CounterKey;
use App\Exceptions\BusinessException;
use App\Models\Counter;
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
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\ScopeBindings;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Where('enrollment', '[0-9]+')]

class EnrollmentController
{
    #[Post('enrollments', 'enrollments.store')]
    #[Authorize('create', Enrollment::class)]
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

    #[Put('enrollments/{enrollment}/payment', 'enrollments.payment-change')]
    #[Authorize('payment', 'enrollment')]
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

    #[Get('enrollments/{enrollment}/full-documents', 'enrollments.full-documents')]
    #[Authorize('statement', 'enrollment')]
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

    #[Get('enrollments/{enrollment}/statements', 'enrollments.statements')]
    #[Authorize('statement', 'enrollment')]
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

    #[Get('enrollments/{enrollment}/approvals', 'enrollments.approvals')]
    #[Authorize('statement', 'enrollment')]
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
    
    #[Delete('exams/{exam}/enrollments/{enrollment}','enrollments.destroy')]
    #[ScopeBindings()]
    #[Authorize('delete', Enrollment::class)]
    public function destroy(
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
    
    #[Put('enrollments/{enrollment}/reg-number', 'enrollments.update.reg-number')]
    #[Authorize('create', Enrollment::class)]
    public function changeRegNumber(
        Enrollment $enrollment, 
        Request $request
    ): Response {
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
        return response()->noContent();
    }
}