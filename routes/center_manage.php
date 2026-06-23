<?php

use App\Http\Controllers\Web\Address\AddressController;
use App\Http\Controllers\Web\Auth\PasswordController;
use App\Http\Controllers\Web\Center\CenterController;
use App\Http\Controllers\Web\Employee\EmployeeController;
use App\Http\Controllers\Web\PlatformAdmin\CounterController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'meta',
    'can:center-manage'
])->group(function () {

    Route::get('centers/{center}', [CenterController::class, 'show'])
        ->can('view', 'center')
        ->name('centers.show');
    Route::get('centers/{center}/employees', [EmployeeController::class, 'index']);
    
    Route::put('centers/{center}', [CenterController::class, 'update'])
        ->can('update', 'center');

    Route::get('centers/{center}/addresses', [AddressController::class, 'index']);
    Route::delete('centers/{center}/addresses/{address}', [AddressController::class, 'destroy'])
        ->name('centers.addresses.destroy');
    Route::patch('centers/{center}/addresses/{address}', [AddressController::class, 'update'])
        ->name('centers.addresses.update');
    Route::post('centers/{center}/addresses', [AddressController::class, 'store'])
        ->name('centers.addresses.store');

    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])
        ->can('delete', 'employee')
        ->name('employees.destroy');

    Route::post('centers/{center}/employees', [EmployeeController::class, 'store']);
    
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])
        ->can('update', 'employee');

    // Route::patch('employees/{employee}/password', [PasswordController::class, 'change'])
    //     ->can('resetPassword', 'employee');

    Route::get('roles', [EmployeeController::class, 'rolesShow']);

    Route::get('centers/{center}/counters', [CounterController::class, 'index']);
    Route::patch('centers/{center}/counters/{counter}', [CounterController::class, 'update']);
});