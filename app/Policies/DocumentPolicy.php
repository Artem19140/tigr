<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Document;
use App\Models\Employee;
use App\Models\ForeignNational;
use Illuminate\Auth\Access\Response;

class DocumentPolicy 
{
    use BasePolicy;
    public function viewAny(Employee $employee): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Examiner
        )){
            return true;
        }
        
        return false;
    }

    public function view(Employee $employee, Document $document): bool
    {
        if($this->notSameCenter($employee, $document)){
            return false;
        }
        $owner = $document->documentable;

        if($owner instanceof ForeignNational){
            if($employee->hasAnyRole(EmployeeRole::Operator)){
                return true;
            }
            
            return $owner
                ->exams()
                ->examiner($employee)
                ->exists();
        }
        
        return true;
    }

    public function create(Employee $employee): bool
    {
        return false;
    }

    public function update(Employee $employee, Document $document): bool
    {
        if($this->notSameCenter($employee, $document)){
            return false;
        }
        
        $owner = $document->documentable;

        if($owner instanceof ForeignNational){
            if($employee->hasAnyRole(EmployeeRole::Operator)){
                return true;
            }
        }

        return false;
    }

    public function delete(Employee $employee, Document $document): bool
    {
        if($this->notSameCenter($employee, $document)){
            return false;
        }

        if($employee->hasAnyRole(EmployeeRole::Operator)){
            return true;
        }
        return false;
    }

    public function restore(Employee $employee, Document $document): bool
    {
        return false;
    }

    public function forceDelete(Employee $employee, Document $document): bool
    {
        return false;
    }
}
