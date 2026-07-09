<?php

namespace App\Http\Controllers\Web\Employee;

use App\Modules\Employee\CreateEmployee;
use App\Modules\Employee\UpdateEmployee;
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
use Inertia\Inertia;

class EmployeeController
{
    public function index(
        Request $request,
    ): \Inertia\Response {

        $notPlatformAdmin = !$request->user()->isPlatformAdmin();

        $employees = Employee::active()
            ->with(['roles'])
            ->when($notPlatformAdmin, function (Builder $query) {
                $query->whereDoesntHave('roles', function (Builder $q) {
                    $q->where('name', EmployeeRole::PlatformAdmin);
                });
            })
            ->orderBy('surname')
            ->get();

        
        return Inertia::render('Center/Center', [
            'employees' => EmployeeResource::collection($employees),
            'tab' => 'employees'
        ]);
    }

    public function store(
        EmployeePostRequest $request,
        CreateEmployee $createEmployee
    ): JsonResponse {

        $createEmployee->execute(
            $request->toDto(),
            $request->user()
        );

        return response()->json();
    }

    public function update(
        EmployeeUpdateRequest $request,
        Employee $employee,
        UpdateEmployee $updateEmployee
    ): JsonResponse {
        $updateEmployee->execute(
            $request->toDto(), 
            $request->user(),
            $employee);

        return response()->json();
    }

    public function destroy(
        Employee $employee
    ): Response {
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
                ->when(! $request->user()->isPlatformAdmin(), function (Builder $query) {
                    $query->where('name', '<>', EmployeeRole::PlatformAdmin)
                        ->where('name', '<>', EmployeeRole::CenterAdmin);
                })
                ->get()
        );
    }
}
