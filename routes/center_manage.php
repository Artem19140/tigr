<?php

use App\Http\Controllers\Web\Address\AddressController;
use App\Http\Controllers\Web\Employee\EmployeeController;
use App\Http\Controllers\Web\CenterManage\CounterController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'meta',
    'can:center-manage'
])->group(function () {
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');

    Route::get('addresses', [AddressController::class, 'index']);
    Route::delete('addresses/{address}', [AddressController::class, 'destroy'])
        ->scopeBindings()
        ->name('addresses.destroy');
    Route::patch('addresses/{address}', [AddressController::class, 'update'])
        ->scopeBindings()
        ->name('addresses.update');
    Route::post('addresses', [AddressController::class, 'store'])
        ->name('addresses.store');

    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])
        ->can('delete', 'employee')
        ->name('employees.destroy');

    Route::post('employees', [EmployeeController::class, 'store']);
    
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])
        ->can('update', 'employee');

    Route::get('roles', [EmployeeController::class, 'rolesShow']);

    Route::get('counters', [CounterController::class, 'index']);
    Route::patch('counters/{counter}', [CounterController::class, 'update'])
        ->scopeBindings();
});