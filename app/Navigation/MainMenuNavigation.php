<?php

namespace App\Navigation;

use App\Models\Employee;
use App\Models\Exam;
use App\Models\ForeignNational;

class MainMenuNavigation
{
    public function resolve(Employee $employee)
    {
        return collect([
            'foreignNationals' => [
                'url' => route('foreign-nationals.index', [], false),
                'can' => $employee->can('viewAny', ForeignNational::class),
            ],
            'exams' => [
                'url' => route('exams.index', [], false),
                'can' => $employee->can('viewAny', Exam::class)
            ],

            'myExams' => [
                'url' => route('my-exams.index', [], false),
                'can' => $employee->can('conductAny', Exam::class)
            ],

            'reports' => [
                'url' => route('reports.resolver', [], false),
                'can' => $employee->can('reports.viewAny')
            ],

            'center' => [
                'url' => route('center-manage.resolver', [], false),
                'can' => $employee->can('center-manage')
            ],

            'admin' => [
                'url' => route('platform-admin.logs', [], false),
                'can' => $employee->can('platform-manage')
            ]
        ])->filter(function($item){
            return $item['can'];
        })->toArray();
    }
}