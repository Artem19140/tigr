<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    use BasePolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Employee $actor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return false;
    }

    public function update(Employee $employee, Employee $actor): bool
    {
        if($this->sameCenter($actor, $employee)){
            return false;
        }
        if($employee->isPlatformAdmin()){
            return false;
        }

        if(
            $employee->hasAnyRole(EmployeeRole::CenterAdmin) 
                && 
            ! $actor->isPlatformAdmin()
        ){
            return false;
        }

        return false;
    }

    public function delete(Employee $employee, Employee $actor): bool
    {
        if($this->sameCenter($actor, $employee)){
            return false;
        }

        if($employee->isPlatformAdmin()){
            return false;
        }

        if(
            $employee->hasAnyRole(EmployeeRole::CenterAdmin) 
                && 
            ! $actor->isPlatformAdmin()
        ){
            return false;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Employee $actor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Employee $actor): bool
    {
        return false;
    }
}
