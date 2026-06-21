<?php

namespace App\Http\Controllers\Web\Exam;

use App\Modules\Attempt\CreateAttempt;
use App\Modules\Exam\CancelExam;
use App\Modules\Exam\CreateExam;
use App\Modules\Exam\UpdateExam;
use App\Modules\Exam\ExamCreateData;
use App\Modules\Exam\ExamViewBuilder;
use App\Modules\Exam\GetExams;
use App\Http\Requests\Exam\ExamIndexRequest;
use App\Http\Requests\Exam\ExamPostRequest;
use App\Http\Requests\Exam\VerifyCodeRequest;
use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\ExamType\ExamTypeResource;
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
        GetExams $getExams
    ): \Inertia\Response {
        Gate::authorize('viewAny', Exam::class);

        $employee = $request->user();
        $dto = $request->toDto();
        $exams = $getExams->execute(
            $dto,
            $employee
        );
        Inertia::flash([
            'filters' => $request->validated(),
            'test' => $dto->toFilters()
        ]);
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
        CreateExam $createExam
    ): JsonResponse {
        
        Gate::authorize('create', Exam::class);
        $createExam->execute(
            $request->toDto(), 
            $request->user()
        );

        return response()->json();
    }

    public function createData(
        ExamCreateData $query
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
        ExamViewBuilder $builder
    ): JsonResponse {
        
        Gate::authorize('view', $exam);
        $employee = $request->user();
        $exam = $builder->execute($exam, $employee);

        return response()->json([
            'exam' => new ExamResource($exam),
        ]);
    }

    public function update(
        ExamPostRequest $request,
        Exam $exam,
        UpdateExam $updateExam
    ): JsonResponse {
        Gate::authorize('update', $exam);

        $updateExam->execute($exam, $request->toDto());

        return response()->json([
            'exam' => new ExamResource($exam),
        ]);
    }

    public function verifyCode(
        VerifyCodeRequest $request,
        CreateAttempt $createAttempt
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
        Request $request,
        Exam $exam,
        CancelExam $cancelExam
    ): Response {
        Gate::authorize('delete', $exam);

        $request->validate([
            'cancelledReason' => ['required', 'string'],
        ]);

        $cancelExam->execute(
            $exam, 
            $request->string('cancelledReason')
        );

        return response()->noContent();
    }
}
