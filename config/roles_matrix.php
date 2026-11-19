<?php

use App\Enums\EmployeeRole;
use App\Enums\Permission;

return [
    EmployeeRole::Operator->value => [
        Permission::ForeignNationalsViewAny->value,
        Permission::ForeignNationalsCreate->value,
        Permission::ForeignNationalsUpdate->value,
        Permission::ForeignNationalsView->value,
        Permission::ForeignNationalsFindMatches->value,

        Permission::ForeignNationalsDocumentsViewAny->value,
        Permission::ForeignNationalsDocumentsView->value,
        Permission::ForeignNationalsDocumentsUpdate->value,

        Permission::ExamsViewAny->value,
        Permission::ExamsCreate->value,
        Permission::ExamsView->value,
        Permission::ExamsUpdate->value,
        Permission::ExamsDelete->value,

        Permission::EnrollmentsCreate->value,
        Permission::EnrollmentsViewAny->value,
        Permission::EnrollmentsDelete->value,
        Permission::EnrollmentsUpdate->value,
        Permission::EnrollmentsChangePayment->value,
        Permission::EnrollmentsStatements->value,

        Permission::ExamsList->value,

        Permission::ReportsFrdo->value
    ],

    EmployeeRole::Examiner->value => [
        Permission::ExamsView->value,
        Permission::ExamsViewAny->value,
        
        Permission::AttemptsEmployeeAccess->value,

        Permission::ExamsList->value,
        Permission::ExamsCodes->value,
        Permission::ExamsResults->value,
        Permission::ExamsProtocol->value,

        Permission::ExamsConduct->value,
        Permission::ExamsReview->value,
    ],

    EmployeeRole::Director->value => [
        Permission::ForeignNationalsViewAny->value,
        Permission::ForeignNationalsView->value,

        Permission::AddressesViewAny->value,
        Permission::AddressesCreate->value,
        Permission::AddressesUpdate->value,
        Permission::AddressesDelete->value,
        Permission::AddressesRestore->value,

        Permission::ReportsFlatTable->value,
        Permission::ReportsFrdo->value,
        Permission::ReportsMinistryEducation->value,

        Permission::EmployeesViewAny->value,
        Permission::EmployeesUpdate->value,
        Permission::EmployeesDelete->value,
        Permission::EmployeesCreate->value,

        Permission::CountersUpdate->value,
        Permission::CountersViewAny->value,

        Permission::CentersEdit->value,
        Permission::CentersView->value,
        Permission::CountersUpdate->value
    ],

    EmployeeRole::CenterAdmin->value => [
        // Permission::AddressesViewAny->value,
        // Permission::AddressesCreate->value,
        // Permission::AddressesUpdate->value,
        // Permission::AddressesDelete->value,
        // Permission::AddressesRestore->value,

        // Permission::EmployeesViewAny->value,
        // Permission::EmployeesUpdate->value,
        // Permission::EmployeesDelete->value,

        // Permission::CentersEdit->value,
        // Permission::CentersView->value,
        // Permission::CountersUpdate->value
    ]
];