<?php

namespace App\Enums;

enum Permission: string
{
    case ExamsViewAny = 'exams.viewAny';
    case ExamsView = 'exams.view';
    case ExamsCreate = 'exams.create';
    case ExamsUpdate = 'exams.update';
    case ExamsDelete = 'exams.delete';
    case ExamsExaminer = 'exams.examiner';
    case ExamsConduct = 'exams.conduct';
    case ExamsReview = 'exams.review';

    case ExamsList = 'exams.list';
    case ExamsCodes = 'exams.codes';
    case ExamsResults = 'exams.results';
    case ExamsProtocol = 'exams.protocol';

    case ForeignNationalsViewAny = 'foreign-nationals.viewAny';
    case ForeignNationalsView = 'foreign-nationals.view';
    case ForeignNationalsCreate = 'foreign-nationals.create';
    case ForeignNationalsUpdate = 'foreign-nationals.update';
    case ForeignNationalsFindMatches = 'foreign-nationals.findMatches';

    case ForeignNationalsDocumentsViewAny = 'foreign-nationals.documents.viewAny';
    case ForeignNationalsDocumentsView = 'foreign-nationals.documents.view';
    case ForeignNationalsDocumentsUpdate = 'foreign-nationals.documents.update';

    case EnrollmentsViewAny = 'enrollments.viewAny';
    case EnrollmentsChangePayment = 'enrollments.change-payment';
    case EnrollmentsCreate = 'enrollments.create';
    case EnrollmentsDelete = 'enrollments.delete';
    case EnrollmentsUpdate = 'enrollments.update';
    case EnrollmentsStatements = 'enrollments.statements';

    case AddressesViewAny = 'addresses.viewAny';
    case AddressesCreate = 'addresses.create';
    case AddressesUpdate = 'addresses.update';
    case AddressesDelete = 'addresses.delete';
    case AddressesRestore = 'addresses.restore';

    case CentersView = 'centers.view';
    case CentersEdit = 'centers.edit';

    case CountersUpdate = 'counters.update';
    case CountersViewAny = 'counters.viewAny';

    case EmployeesViewAny = 'employees.viewAny';
    case EmployeesCreate = 'employees.create';
    case EmployeesUpdate = 'employees.update';
    case EmployeesDelete = 'employees.delete';

    case ReportsFrdo = 'reports.frdo';
    case ReportsMinistryEducation = 'reports.ministry-education';
    case ReportsFlatTable = 'reports.flat-table';

    case AttemptsEmployeeAccess = 'attempts.employee-access';

    case Statistics = 'statistics';

    public function label(): string 
    {
        return __("permissions.$this->value");
    }
}
