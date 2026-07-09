<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\Exam;

class ExamPolicy
{
    public function viewAny(Employee $employee): bool
    {
        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director,
            EmployeeRole::Examiner,
            EmployeeRole::Scheduler,
            EmployeeRole::VideoRecordOperator
        )) {
            return true;
        }

        return false;
    }

    public function view(Employee $employee, Exam $exam): bool
    {
        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director,
            EmployeeRole::Scheduler,
            EmployeeRole::VideoRecordOperator
        )) {
            return true;
        }

        return $this->examiner($employee, $exam);
    }

    public function conductAny(Employee $employee):bool
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
        return $employee->hasAnyRole(EmployeeRole::Scheduler);
    }

    public function delete(Employee $employee, Exam $exam): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Scheduler);
    }

    public function examiner(Employee $employee, Exam $exam): bool
    {
        if (! $employee->hasRole(EmployeeRole::Examiner->value)) {
            return false;
        }

        return $employee->exams()
            ->wherePivot('exam_id', $exam->id)
            ->exists();
    }


    public function list(Employee $employee, Exam $exam): bool
    {
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
        if ($employee->hasAnyRole(
            EmployeeRole::Director
        )) {
            return true;
        }

        return $this->examiner($employee, $exam);
    }

    public function protocol(Employee $employee, Exam $exam): bool
    {
        if ($employee->hasAnyRole(
            EmployeeRole::Director
        )) {
            return true;
        }

        return $this->examiner($employee, $exam);
    }

    public function videos(Employee $employee, Exam $exam): bool{
        return $employee->hasAnyRole(EmployeeRole::VideoRecordOperator);
    }
}
