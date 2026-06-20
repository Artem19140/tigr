<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Modules\Enrollment\ChangePaymentStatus;
use App\Modules\Enrollment\CreateEnrollment;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

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
    ): JsonResponse {

        Gate::authorize('payment', $enrollment);
        $changePaymentStatus->execute($enrollment);

        return response()->json();
    }
}
