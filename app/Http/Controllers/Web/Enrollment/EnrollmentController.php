<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Modules\Enrollment\Action\ChangePaymentStatusAction;
use App\Modules\Enrollment\Action\CreateEnrollmentAction;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class EnrollmentController
{
    public function store(
        EnrollmentStoreRequest $request,
        CreateEnrollmentAction $createEnrollmentAction,
    ): JsonResponse {
        $enrollment = $createEnrollmentAction->execute(
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
        ChangePaymentStatusAction $changePaymentStatusAction
    ): JsonResponse {

        Gate::authorize('payment', $enrollment);
        $changePaymentStatusAction->execute($enrollment);

        return response()->json();
    }
}
