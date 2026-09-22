<?php

namespace App\Http\Controllers\Web\Exam;

use App\Enums\AvailabilityCode;
use App\Http\Resources\Exam\ExamEditResource;
use App\Http\Resources\Exam\ExamResource;
use App\Modules\Exam\CancelExam;
use App\Modules\Exam\CreateExam;
use App\Modules\Exam\ExamCancellRules;
use App\Modules\Exam\ExamEditRules;
use App\Modules\Exam\ExamViewBuilder;
use App\Modules\Exam\UpdateExam;
use App\Modules\Exam\ExamCreateData;
use App\Modules\Exam\GetExams;
use App\Http\Requests\Exam\ExamIndexRequest;
use App\Http\Requests\Exam\ExamPostRequest;
use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Http\Resources\ExamType\ExamTypeResource;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'filters' => $dto->toFilters()
        ]);

        $employee = $request->user();
        
        return Inertia::render('Exam/Index', [
            'createUrl' => $employee->can('create', Exam::class)
                ? route('exams.create', [], false)
                : null,
            'exams' => ExamIndexResource::collection($exams)
        ]);
    }

    public function show(
        Request $request,
        Exam $exam,
        ExamViewBuilder $builder
    ): \Inertia\Response {

        $employee = $request->user();

        $exam = $builder->execute($exam, $employee);

        return Inertia::render('Exam/View',[
            'exam' => new ExamResource($exam),
            'actions' => [
                'edit' => [
                    'url' => $employee->can('update', $exam)
                        ? route('exams.edit', [
                            'exam' => $exam
                        ], false)
                        : null,
                    'disabled' => ! app(ExamEditRules::class)->check($exam)->available
                ],
                'destroy' => [
                    'url' => $employee->can('delete', $exam)
                        ? route('exams.destroy', [
                            'exam' => $exam
                        ], false)
                        : null,
                    'disabled' => ! app(ExamCancellRules::class)->check($exam)->available
                ]
            ],
            'documents' => app(ExamDocumentBuilder::class)->build($exam, $request->user()),
            'reviewStatus' => AvailabilityCode::ExamOnReview->value
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

    public function create(
        ExamCreateData $builder
    ): \Inertia\Response {
        Gate::authorize('create', Exam::class);

        $createData = $builder->execute();
        return Inertia::render('Exam/Create', [
            'addresses' => AddressResource::collection($createData['addresses']),
            'examTypes' => ExamTypeResource::collection($createData['examTypes']),
            'examiners' => EmployeeResource::collection($createData['examiners']),
        ]);
    }

    public function edit(
        Exam $exam,
        ExamCreateData $builder
    ): \Inertia\Response {

        Gate::authorize('update', $exam);
        $createData = $builder->execute();
        $exam->load('examiners');

        return Inertia::render('Exam/Edit', [
            'exam' => new ExamEditResource($exam),
            'addresses' => AddressResource::collection($createData['addresses']),
            'examTypes' => ExamTypeResource::collection($createData['examTypes']),
            'examiners' => EmployeeResource::collection($createData['examiners']),
            'backUrl' => route('exams.show', [
                'exam' => $exam
            ], false),
            'updateUrl' => route('exams.update', [
                'exam' => $exam
            ], false)
        ]);
    }

    public function update(
        ExamPostRequest $request,
        Exam $exam,
        UpdateExam $updateExam
    ):RedirectResponse {
        Gate::authorize('update', $exam);

        $updateExam->execute($exam, $request->toDto());

        return redirect()->route('exams.show', [
            'exam' => $exam
        ]);
    }

    public function destroy(
        Request $request,
        Exam $exam,
        CancelExam $cancelExam
    ): RedirectResponse {
        Gate::authorize('delete', $exam);

        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $cancelExam->execute(
            $exam, 
            $request->string('reason')
        );

        return redirect()->route('exams.show', [
            'exam' => $exam
        ]);
    }
}
