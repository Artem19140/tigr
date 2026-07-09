<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;

class EmployeePolicy
{
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

    public function update(Employee $actor, Employee $employee ): bool
    {
        
        
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

        if($actor->hasAnyRole(EmployeeRole::CenterAdmin)){
            return true;
        }

        return false;
    }

    public function delete(Employee $actor, Employee $employee): bool
    {


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

        if($actor->hasAnyRole(EmployeeRole::CenterAdmin)){
            return true;
        }

        return false;
    }
    public function resetPassword(Employee $actor , Employee $employee): bool
    {
        if($employee->isPlatformAdmin()){
            return false;
        }

        if($actor->hasAnyRole(EmployeeRole::CenterAdmin)){
            return true;
        }
        
        return false;
    }

}
