<?php

namespace App\Http\Controllers\Web\ForeignNational;

use App\Domain\ForeignNational\Action\CreateForeignNationalWithEnrollmentAction;
use App\Domain\ForeignNational\Action\UpdateForeignNationalAction;
use App\Domain\ForeignNational\Query\GetForeignNationalsQuery;
use App\Enums\EmployeeRole;
use App\Http\Requests\ForeignNational\ForeignNationalIndexRequest;
use App\Http\Requests\ForeignNational\ForeignNationalPostRequest;
use App\Http\Requests\ForeignNational\ForeignNationalUpdateRequest;
use App\Http\Resources\ForeignNational\ForeignNationalIndexResource;
use App\Http\Resources\ForeignNational\ForeignNationalProfileResource;
use App\Models\Document;
use App\Models\Enrollment;
use App\Models\ForeignNational;
use App\Support\CenterIsolationCheck;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ForeignNationalController
{
    public function index(
        ForeignNationalIndexRequest $request,
        GetForeignNationalsQuery $getForeignNationalsQuery
    ): Response {

        Gate::authorize('viewAny', ForeignNational::class);

        $foreignNationals = $getForeignNationalsQuery->execute($request->validated() ?? []);

        Inertia::flash(['filters' => request()->all()]);

        $employee = $request->user();
        CenterIsolationCheck::check($foreignNationals);
        return Inertia::render('ForeignNationals/ForeignNationals', [
            'foreignNationals' => ForeignNationalIndexResource::collection($foreignNationals),
            'permissions' => [
                'create' => $employee->can('create', ForeignNational::class),
                'export' => $employee->can('export', ForeignNational::class),
                'statistics' => $employee->can('statistics'),
                'ministryEducation' => $employee->hasAnyRole(EmployeeRole::Director, EmployeeRole::PlatformAdmin),
            ],
        ]);
    }

    public function store(
        ForeignNationalPostRequest $request,
        CreateForeignNationalWithEnrollmentAction $createForeignNationalWithEnrollmentAction
    ): JsonResponse {
        Gate::authorize('create', ForeignNational::class);
        $enrollement = $createForeignNationalWithEnrollmentAction
            ->execute(
                $request->validated(),
                $request->validated('examId'),
                $request->user()
            );

        return response()->json([
            'redirectUrl' => route('enrollments.statements', [
                'enrollment' => $enrollement
            ]),
        ]);
    }

    public function show(
        Request $request,
        ForeignNational $foreignNational
    ): JsonResponse {
        Gate::authorize('view', $foreignNational);

        $foreignNational->load([
            'creator',
            'enrollments' => function ($query) use ($request) {
                $query->visibleFor($request->user())
                    ->with([
                        'exam' => ['type', 'center'],
                        'attempt.center',
                    ]);
            }
        ]);

        if($request->user()->can('viewAny', Document::class)){
            $foreignNational->load([
                'documents' => function(MorphMany $query){
                    return $query->whereNull('deleted_at');
                },
                'documents.creator'
            ]);
        }

        $foreignNational->enrollments = $foreignNational->enrollments->sortByDesc('exam.begin_time');
        $foreignNational->enrollments->loadExists('attempt');
        $employee = $request->user();

        return response()->json([
            'foreignNational' => new ForeignNationalProfileResource($foreignNational),
            'permissions' => [
                'enroll' => $employee->can('create', Enrollment::class),
                'edit' => $employee->can('update', $foreignNational),
                'documents' => $employee->can('viewAny', Document::class),
                'enrollments' => [
                    'statement' => $employee->hasAnyRole(EmployeeRole::Operator, EmployeeRole::PlatformAdmin),
                    'payment' => $employee->hasAnyRole(EmployeeRole::Operator, EmployeeRole::PlatformAdmin),
                ],
            ],
        ]);
    }

    public function update(
        ForeignNationalUpdateRequest $request,
        ForeignNational $foreignNational,
        UpdateForeignNationalAction $updateForeignNationalAction
    ): JsonResponse {
        $updatedForeignNational = $updateForeignNationalAction->execute(
            $request->validated(),
            $foreignNational
        );

        return response()->json([
            'foreignNational' => new ForeignNationalProfileResource($updatedForeignNational),
        ]);
    }
}
