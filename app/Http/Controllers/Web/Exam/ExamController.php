<?php

namespace App\Http\Controllers\Web\Exam;

use App\Domain\Attempt\Action\CreateAttemptAction;
use App\Domain\Exam\Action\CancelExamAction;
use App\Domain\Exam\Action\CreateExamAction;
use App\Domain\Exam\Action\UpdateExamAction;
use App\Domain\Exam\Query\ExamCreateDataQuery;
use App\Domain\Exam\Query\ExamShowQuery;
use App\Domain\Exam\Query\GetExamsQuery;
use App\Enums\EmployeeRole;
use App\Http\Requests\Exam\ExamIndexRequest;
use App\Http\Requests\Exam\ExamPostRequest;
use App\Http\Requests\Exam\VerifyCodeRequest;
use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\ExamType\ExamTypeResource;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ExamController
{
    public function index(
        ExamIndexRequest $request,
        GetExamsQuery $getExamQuery
    ): \Inertia\Response {
        Gate::authorize('viewAny', Exam::class);
        $exams = $getExamQuery->execute($request->validated() ?? []);
        Inertia::flash('filters', request()->all());
        CenterIsolationCheck::check($exams);
        $employee = $request->user();
        
        return Inertia::render('Exam/Exam', [
            'permissions' => [
                'create' => $employee->can('create', Exam::class),
                'flatTable' => $employee->can('reports.flat-table'),
                'frdo' => $employee->can('reports.frdo'),
            ],
            'exams' => ExamIndexResource::collection($exams)
        ]);
    }

    public function store(
        ExamPostRequest $request,
        CreateExamAction $createExamAction
    ): JsonResponse {
        
        Gate::authorize('create', Exam::class);
        $createExamAction->execute($request->getDto(), $request->user());

        return response()->json();
    }

    public function createData(
        ExamCreateDataQuery $query
    ): JsonResponse
    {
        Gate::authorize('create', Exam::class);
        $createData = $query->execute();
        CenterIsolationCheck::check($createData['addresses']);
        CenterIsolationCheck::check($createData['examiners']);
        return response()->json([
            'addresses' => AddressResource::collection($createData['addresses']),
            'examTypes' => ExamTypeResource::collection($createData['examTypes']),
            'examiners' => EmployeeResource::collection($createData['examiners']),
        ], 200);
    }

    public function show(
        Request $request,
        Exam $exam,
        ExamShowQuery $examShowQuery
    ): JsonResponse {
        
        Gate::authorize('view', $exam);
        $employee = $request->user();
        $exam = $examShowQuery->execute($exam);

        return response()->json([
            'permissions' => [
                'documents' => [
                    'codes' => $employee->can('examiner', $exam),
                    'protocol' => $employee->can('protocol', $exam),
                    'results' => $employee->can('results', $exam),
                    'list' => $employee->can('list', $exam),
                ],
                'actions' => [
                    'edit' => $employee->can('update', $exam),
                    'delete' => $employee->can('delete', $exam),
                ],
                'enrollments' => [
                    'view' => $employee->can('viewAny', Enrollment::class),
                    'statement' => $employee->can('statementAny', Enrollment::class),
                    'payment' => $employee->can('paymentAny', Enrollment::class),
                ],
                'videos' => [
                    'view' => $employee->can('video', Exam::class)
                ]

            ],
            'exam' => new ExamResource($exam),
        ]);
    }

    public function update(
        ExamPostRequest $request,
        Exam $exam,
        UpdateExamAction $updateExam
    ): JsonResponse {
        Gate::authorize('update', $exam);

        $updateExam->execute($exam, $request->getDto());

        return response()->json([
            'exam' => new ExamResource($exam),
        ]);
    }

    public function verifyCode(
        VerifyCodeRequest $request,
        CreateAttemptAction $createAttempt
    ): RedirectResponse {
        $attempt = $createAttempt->execute($request->validated('code'));

        Auth::guard('foreignNationals')
            ->login($attempt->foreignNational);

        $request->session()->regenerate();

        return redirect()->route('attempts.show', [
            'attempt' => $attempt->id,
        ]);
    }

    public function destroy(
        Exam $exam,
        CancelExamAction $cancelExam
    ): Response {
        Gate::authorize('delete', $exam);

        request()->validate([
            'cancelledReason' => ['required', 'string'],
        ]);

        $cancelExam->execute($exam, request()->string('cancelledReason'));

        return response()->noContent();
    }
}
