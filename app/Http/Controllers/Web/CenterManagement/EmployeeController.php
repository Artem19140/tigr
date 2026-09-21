<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Resources\Employee\EmployeeIndexResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Modules\Employee\CreateEmployee;
use App\Modules\Employee\UpdateEmployee;
use App\Enums\EmployeeRole;
use App\Exceptions\BusinessException;
use App\Http\Requests\Employee\EmployeePostRequest;
use App\Http\Requests\Employee\EmployeeUpdateRequest;
use App\Http\Resources\Role\RoleResource;
use App\Models\Employee;
use App\Models\Role;
use App\Support\Audit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class EmployeeController
{
    public function index(
        Request $request
    ): \Inertia\Response {
        $employees = Employee::query()
            ->with('roles')
            ->whereDoesntHave('roles', function (Builder $q) {
                $q->where('name', EmployeeRole::PlatformAdmin);
            })
            ->orderBy('surname')
            ->where('is_active', true)
            ->get();

        return Inertia::render('CenterManage/Employees', [
            'employees' => EmployeeIndexResource::collection($employees),
            'createUrl' => $request->user()->can('create', Employee::class)
                ? route('employees.create', [], false)
                : null,
        ]);
    }

    public function create() {
        Gate::authorize('create', Employee::class);
        return Inertia::render('CenterManage/EmployeeCreate', [
            'backUrl' => route('employees.index',[], false),
            'storeUrl' => route('employees.store'),
        ]);
    }

    public function store(
        EmployeePostRequest $request,
        CreateEmployee $createEmployee
    ): RedirectResponse {
        Gate::authorize('create', Employee::class);
        $createEmployee->execute(
            $request->toDto(),
            $request->user()
        );

        return redirect()->route('employees.index');
    }

    public function edit(Employee $employee) {
        Gate::authorize('update', $employee);
        $employee->load('roles');
        return Inertia::render('CenterManage/EmployeeEdit', [
            'backUrl' => route('employees.index',[], false),
            'employee' => new EmployeeResource($employee),
            'updateUrl' => route('employees.update', [
                'employee' => $employee
            ])
        ]);
    }
    

    public function update(
        EmployeeUpdateRequest $request,
        Employee $employee,
        UpdateEmployee $updateEmployee
    ): RedirectResponse {
        Gate::authorize('update', $employee);
        $updateEmployee->execute(
            $request->toDto(), 
            $request->user(),
            $employee);

        return redirect()->route('employees.index');
    }

    public function destroy(
        Employee $employee,
        Audit $audit
    ) : RedirectResponse {
        if (! $employee->isActive()) {
            throw new BusinessException('Сотрудник уже уволен');
        }

        $employee->update([
            'is_active' => false
        ]);

        $audit->log('employee_fired', $employee);
        return back();
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
