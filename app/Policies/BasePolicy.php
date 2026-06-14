<?php

namespace App\Policies;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait BasePolicy
{
    public function notSameCenter(Employee $employee, Model $model): bool
    {
        if ($employee->center_id !== $model->center_id) {
            Log::warning('UNAUTHORIZED: center access',[
                'employee_center_id' => $employee->center_id,
                'employee_id' => $employee->id,
                'model_center_id' => $model->center_id,
                'model_id' => $model->id,
                'model_type' => class_basename($model),
                'url' => request()->url(),
            ]);
            return true;
        }
        return false;
    }
}
