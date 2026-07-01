<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Modules\Enrollment\ChangePaymentStatus;
use App\Modules\Enrollment\CreateEnrollment;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;

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

        // return Inertia::flash([
        //     'redirectUrl' => route('enrollments.statements', [
        //         'enrollment' => $enrollment,
        //     ])
        // ])->back();
    }

    public function changePayment(
        Enrollment $enrollment,
        ChangePaymentStatus $changePaymentStatus
    ): JsonResponse {
        $changePaymentStatus->execute($enrollment);

        return response()->json();
    }
}
