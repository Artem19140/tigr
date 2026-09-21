<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $employee = $request->user();
        return [
            'id' => $this->resource->id,
            'fullName' => $this->resource->surname.' '.$this->resource->name.' '.$this->resource->patronymic,
            'email' => $this->resource->email,
            'destroyUrl' => $employee->can('delete', $this->resource)
                ? route('employees.destroy', ['employee' => $this->resource], false)
                : null,
            'editUrl' => $employee->can('update', $this->resource)
                ? route('employees.edit', ['employee' => $this->resource], false)
                : null,
            'isActive' => $this->resource->is_active
        ];
    }
}
