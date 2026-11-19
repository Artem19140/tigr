<?php

namespace App\Http\Controllers\Web\PlatformManage;

use App\Enums\EmployeeRole;
use App\Enums\Permission;
use App\Models\Role;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;

#[Middleware('auth:web')]
#[Authorize('platform-manage')]

class RolesMatrixController
{
    #[Get('admin/roles-matrix', 'platform-admin.roles-matrix')]
    public function index()
    {
        $matrix = config('roles_matrix');

        $permissions = collect(Permission::cases());

        $roles = Role::where('name', '<>', EmployeeRole::PlatformAdmin)
            ->get();
        
        return Inertia::render('PlatformAdmin/RolesMatrix', [
            'roles' => $roles->pluck('name'),
            'permissions' => $permissions->map(fn ($permission) => [
                'value' => $permission->value,
                'label' => $permission->label(),
                'roles' => $roles
                    ->filter(fn (Role $role) => \in_array(
                        $permission->value,
                        $matrix[$role->name->value]
                    ))->pluck('name')
                    
            ])->values(),
        ]);
    }
}