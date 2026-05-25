<?php

namespace App\Http\Controllers\Web\Employee;

use App\Domain\Center\CenterContext;
use App\Domain\Employee\CreateEmployeeAction;
use App\Domain\Employee\UpdateEmployeeAction;
use App\Enums\EmployeeRole;
use App\Exceptions\BusinessException;
use App\Http\Requests\Employee\EmployeePostRequest;
use App\Http\Requests\Employee\EmployeeUpdateRequest;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EmployeeController
{
    public function index(Request $request): \Inertia\Response
    {
        $notSuperAdmin = ! $request->user()->isSuperAdmin();
        $employees = Employee::active()
            ->forCenter(app(CenterContext::class)->id())
            ->with(['roles'])
            ->when($notSuperAdmin, function (Builder $query) {
                $query->whereDoesntHave('roles', function (Builder $q) {
                    $q->where('name', EmployeeRole::SuperAdmin);
                });
            })
            ->orderBy('surname')
            ->get();
        Log::info('employees_view', []);

        return Inertia::render('Center/Center', [
            'employees' => EmployeeResource::collection($employees),
            'tab' => 'employees',
        ]);
    }

    public function store(
        EmployeePostRequest $request,
        CreateEmployeeAction $createEmployee
    ): JsonResponse {
        $createEmployee->execute($request->validated(), $request->user());

        return response()->json();
    }

    public function update(
        EmployeeUpdateRequest $request,
        Employee $employee,
        UpdateEmployeeAction $updateEmployeeAction
    ): JsonResponse {
        if ($employee->isSuperAdmin()) {
            abort(403);
        }
        $updateEmployeeAction->execute($request->validated(), $employee);

        return response()->json();
    }

    public function destroy(
        Employee $employee,
        Request $request
    ): Response {
        if ($request->user()->center_id !== $employee->center_id && ! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($employee->isSuperAdmin()) {
            abort(403);
        }

        if ($employee->hasRole(EmployeeRole::CenterAdmin->value) && ! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if (! $employee->isActive()) {
            throw new BusinessException('Сотрудник уволен');
        }

        $employee->is_active = false;

        $employee->save();

        return response()->noContent();
    }

    public function rolesShow(Request $request): AnonymousResourceCollection
    {
        return RoleResource::collection(
            Role::select(['id', 'name'])
                ->when(! $request->user()->isSuperAdmin(), function (Builder $query) {
                    $query->where('name', '<>', EmployeeRole::SuperAdmin)
                        ->where('name', '<>', EmployeeRole::CenterAdmin);
                })
                ->get()
        );
    }
}
