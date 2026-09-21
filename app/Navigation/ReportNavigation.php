<?php

namespace App\Navigation;

use App\Models\Employee;

class ReportNavigation
{
    public function resolve(Employee $employee)
    {
        return collect([
            'frdo' => [
                'url' => route('reports.frdo', [], false),
                'can' =>   $employee->can('reports.frdo')
            ],

            'flatTable' => [
                'url' => route('reports.flat-table', [], false),
                'can' => $employee->can('reports.flat-table'),
            ],
            
            'ministryEducation' => [
                'url' => route('reports.ministry-education', [], false),
                'can' => $employee->can('reports.ministry-education')
            ]
        ])->filter(function($item){
            return $item['can'];
        });
    }
}