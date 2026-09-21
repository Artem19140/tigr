<?php

use App\Http\Controllers\Web\CenterManagement\CounterController;
use App\Http\Controllers\Web\CenterManagement\EmployeeController;
use App\Http\Controllers\Web\CenterManagement\AddressController;
use App\Http\Controllers\Web\CenterManagement\CenterController;
use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use App\Models\Center;
use App\Models\Counter;
use App\Models\Employee;
use App\Navigation\CenterManageNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([
    'meta',
    'can:center-manage'
])->group(function () {
    Route::get('center-manage/addresses', [AddressController::class, 'index'])
        ->can('viewAny', Address::class)
        ->name('addresses.index');

    Route::delete('addresses/{address}', [AddressController::class, 'destroy'])
        ->can('delete', 'address')
        ->name('addresses.destroy');

    Route::patch('center-manage/addresses/{address}', [AddressController::class, 'update'])
        ->can('update', 'address')
        ->name('addresses.update');

    Route::post('addresses', [AddressController::class, 'store'])
        ->can('create', Address::class)
        ->name('addresses.store');
    
    Route::get('addresses/create', [AddressController::class, 'create'])
        ->can('create', Address::class)
        ->name('addresses.create'); 
    
    Route::get('addresses/{address}/edit', function(Address $address){
        $address->loadExists(['exams as examsExists']);
        return Inertia::render('CenterManage/AddressEdit', [
            'address' => new AddressResource($address),
            'updateUrl' => route('addresses.update', ['address' => $address], false),
            'backUrl' => route('addresses.index', [], false)
        ]);
    })
        ->can('update', 'address')
        ->name('addresses.edit');

    Route::resource('employees', EmployeeController::class)
        ->except('index');

    Route::get('center-manage/employees', [EmployeeController::class, 'index'])
        ->can('viewAny', Employee::class)
        ->name('employees.index');

    Route::get('roles', [EmployeeController::class, 'rolesShow']);

    Route::get('center-manage/counters', [CounterController::class, 'index'])
        ->name('counters.index');

    Route::patch('counters/{counter}', [CounterController::class, 'update'])
        ->can('update', Counter::class)
        ->name('counters.update');

    Route::get('center-manage', [CenterController::class, 'show'])
        ->can('view', Center::class)
        ->name('centers.show');

    Route::get('center-manage/edit', [CenterController::class, 'edit'])
        ->can('edit', Center::class)
        ->name('centers.edit');

    Route::put('centers/{center}', [CenterController::class, 'update'])
        ->can('edit', Center::class)
        ->name('centers.update');

    Route::get('center-manage/resolver', function(
        Request $request,
        CenterManageNavigation $navigation
    ){
        $employee = $request->user();
        $allowedRoutes = $navigation->resolve($employee);

        if($allowedRoutes->isEmpty()){
            Log::warning('UNEXPECTED: reports route not resolved ', [
                'employee' => $employee->id,
                'route' => $request->route()
            ]);
            abort(403);
        }
        return redirect($allowedRoutes->first()['url']);
    })->name('center-manage.resolver');
    
});