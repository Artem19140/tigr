<?php

namespace App\Navigation;

use App\Models\Address;
use App\Models\Counter;
use App\Models\Employee;

class CenterManageNavigation
{
    public function resolve(Employee $employee)
    {    
        return collect([
            'data' => [
                'url' => route('centers.show', [], false),
                'can' => $employee->can('center-manage')
            ],
            'employees' => [
                'url' => route('employees.index', [], false),
                'can' => $employee->can('viewAny', Employee::class)
            ],
            'addresses' => [
                'url' => route('addresses.index', [], false),
                'can' => $employee->can('viewAny', Address::class)
            ],
            'counters' => [
                'url' => route('counters.index', [], false),
                'can' => $employee->can('viewAny', Counter::class)
            ]
        ])->filter(function($item){
            return $item['can'];
        });
    }

}