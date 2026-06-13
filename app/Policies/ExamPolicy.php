<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\Exam;

class ExamPolicy
{
    use BasePolicy;

    public function viewAny(Employee $employee): bool
    {
        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director,
            EmployeeRole::Examiner,
            EmployeeRole::Scheduler
        )) {
            return true;
        }

        return false;
    }

    public function view(Employee $employee, Exam $exam): bool
    {
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }
        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director,
            EmployeeRole::Scheduler
        )) {
            return true;
        }

        return $this->examiner($employee, $exam);
    }

    public function monitoringAny(Employee $employee):bool
    {
        return $employee->hasAnyRole(EmployeeRole::Examiner);
    }

    public function checkingAny(Employee $employee):bool
    {
        return $employee->hasAnyRole(EmployeeRole::Examiner);
    }

    public function create(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Scheduler);
    }

    public function update(Employee $employee, Exam $exam): bool
    {
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }

        return $employee->hasAnyRole(EmployeeRole::Scheduler);
    }

    public function delete(Employee $employee, Exam $exam): bool
    {
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }

        return $employee->hasAnyRole(EmployeeRole::Scheduler);
    }

    public function examiner(Employee $employee, Exam $exam): bool
    {
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }
        if (! $employee->hasRole(EmployeeRole::Examiner->value)) {
            return false;
        }

        return $employee->exams()
            ->wherePivot('exam_id', $exam->id)
            ->exists();
    }


    public function list(Employee $employee, Exam $exam): bool
    {
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }
        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director
        )) {
            return true;
        }

        return $this->examiner($employee, $exam);
    }

    public function results(Employee $employee, Exam $exam): bool
    {
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }
        if ($employee->hasAnyRole(
            EmployeeRole::Director
        )) {
            return true;
        }

        return $this->examiner($employee, $exam);
    }

    public function protocol(Employee $employee, Exam $exam): bool
    {
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }
        if ($employee->hasAnyRole(
            EmployeeRole::Director
        )) {
            return true;
        }

        return $this->examiner($employee, $exam);
    }

    public function video(Employee $employee, Exam $exam): bool{
        if (! $this->sameCenter($employee, $exam)) {
            return false;
        }
        return $employee->hasAnyRole(EmployeeRole::VideoRecordOperator);
    }
}
