<?php

namespace App\Providers;

use App\Enums\EmployeeRole;
use App\Models\Attempt;
use App\Models\Employee;
use App\Models\ForeignNational;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('attempts.foreign-national-access', function (
            ForeignNational $foreignNational, 
            Attempt $attempt
        ) 
        {
            return $foreignNational->id === $attempt->foreign_national_id;
        });

        Gate::define('statistics', function (Employee $employee) {
            return $employee->hasAnyRole(EmployeeRole::Director);
        });

        Gate::define('reports.frdo', function (Employee $employee) {
            return $employee->hasAnyRole(EmployeeRole::Director, EmployeeRole::Operator);
        });

        Gate::define('reports.ministry-education', function (Employee $employee) {
            return $employee->hasAnyRole(EmployeeRole::Director);
        });

        Gate::define('reports.flat-table', function (Employee $employee) {
            return $employee->hasAnyRole(EmployeeRole::Director);
        });

        Gate::define('reports.viewAny', function (Employee $employee) {
            return 
                $employee->can('reports.ministry-education') ||
                $employee->can('reports.flat-table') ||
                $employee->can('reports.frdo')
            ;
        });

        Gate::define('platform-manage', function (Employee $employee) {
            return $employee->hasAnyRole(EmployeeRole::PlatformAdmin);
        });

        Gate::define('center-manage', function (Employee $employee) {
            return $employee->hasAnyRole(EmployeeRole::CenterAdmin);
        });

        Gate::define('attempts.employee-access', function (Employee $employee, Attempt $attempt) {
            if(! $employee->hasAnyRole(EmployeeRole::Examiner)){
                return false;
            }

            return $attempt->exam()
                ->examiner($employee)
                ->exists();
        });

        Gate::before(function (Employee|ForeignNational $user, string $ability) {
            if ($user instanceof ForeignNational) {
                return null;
            }
            if ($user->isPlatformAdmin()) {
                return true;
            }

            return null;
        });
    }
}
