<?php

namespace App\Http\Controllers\Web\Employee;

use App\Domain\Employee\CreateEmployeeAction;
use App\Domain\Employee\UpdateEmployeeAction;
use App\Enums\EmployeeRole;
use App\Exceptions\BusinessException;
use App\Http\Requests\Employee\EmployeePostRequest;
use App\Http\Requests\Employee\EmployeeUpdateRequest;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Center;
use App\Models\Employee;
use App\Models\Role;
use App\Support\CenterIsolationCheck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EmployeeController
{
    public function index(
        Request $request, 
        Center $center
    ): \Inertia\Response {
        //abort_if($center->id !== request()->user()->center_id, 404);
        $notPlatformAdmin = !$request->user()->isPlatformAdmin();

        $employees = Employee::active()
            ->forCenter($center->id)
            ->with(['roles'])
            ->when($notPlatformAdmin, function (Builder $query) {
                $query->whereDoesntHave('roles', function (Builder $q) {
                    $q->where('name', EmployeeRole::PlatformAdmin);
                });
            })
            ->orderBy('surname')
            ->get();
            
        Log::info('employees_view', []);

        CenterIsolationCheck::check($employees);
        
        return Inertia::render('Center/Center', [
            'employees' => EmployeeResource::collection($employees),
            'tab' => 'employees',
            'centerId' => $center->id
        ]);
    }

    public function store(
        EmployeePostRequest $request,
        CreateEmployeeAction $createEmployee,
        Center $center
    ): JsonResponse {
        //abort_if($center->id !== request()->user()->center_id, 404);
        $createEmployee->execute(
            $request->validated(),
            $center,
            $request->user()
        );
        return response()->json();
    }

    public function update(
        EmployeeUpdateRequest $request,
        Employee $employee,
        UpdateEmployeeAction $updateEmployeeAction
    ): JsonResponse {
        $updateEmployeeAction->execute($request->validated(), $employee);

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
