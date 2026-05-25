<?php

namespace App\Http\Controllers\Web\Exam;

use App\Domain\Attempt\Action\CreateAttemptAction;
use App\Domain\Center\CenterContext;
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
use App\Http\Resources\Exam\ExamCalendarResource;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\ExamType\ExamTypeResource;
use App\Models\Enrollment;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ExamController
{
    public function __construct(
        protected CenterContext $centerContext
    ){}
    public function index(
        ExamIndexRequest $request,
        GetExamsQuery $getExamQuery
    ): \Inertia\Response {
        Gate::authorize('viewAny', Exam::class);
        $exams = $getExamQuery->execute($request->validated() ?? []);
        Inertia::flash('filters', request()->all());

        $employee = $request->user();

        return Inertia::render('Exam/Exam', [
            'permissions' => [
                'create' => $employee->can('create', Exam::class),
                'flatTable' => $employee->hasRole(EmployeeRole::Director->value),
                'frdo' => $employee->can('frdo', Exam::class),
            ],
            'exams' => ExamIndexResource::collection($exams),

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
                    'protocol' => $employee->can('examiner', $exam),
                    'results' => $employee->can('examiner', $exam),
                    'list' => $employee->can('list', $exam),
                ],
                'actions' => [
                    'edit' => $employee->can('update', $exam),
                    'delete' => $employee->can('delete', $exam),
                ],
                'enrollments' => [
                    'view' => $employee->can('viewAny', Enrollment::class),
                    'statement' => $employee->hasAnyRole(EmployeeRole::Operator),
                    'payment' => $employee->hasAnyRole(EmployeeRole::Operator) || $employee->can('examiner', $exam),
                ],

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

    public function schedule(Request $request): \Inertia\Response
    {
        $request->validate([
            'dateFrom' => ['sometimes', 'date'],
            'dateTo' => ['sometimes', 'date'],
        ]);

        $dateFrom = Carbon::parse($request->input('dateFrom'))
            ->copy()->startOfDay();

        $dateTo = Carbon::parse($request->input('dateTo'))
            ->copy()->endOfDay();

        $exams = Exam::query()
            ->forCenter($this->centerContext->id())
            ->visibleFor($request->user())
            ->with(['type', 'center'])
            ->where('begin_time', '>=', $dateFrom)
            ->where('begin_time', '<', $dateTo)
            ->get();

        return Inertia::render('Schedule/Schedule', [
            'exams' => ExamCalendarResource::collection($exams),
            'permissions' => [
                'create' => $request->user()->can('create', Exam::class),
            ],
        ]);
    }
}
