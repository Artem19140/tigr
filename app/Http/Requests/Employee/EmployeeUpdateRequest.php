<?php

namespace App\Http\Requests\Employee;

use App\Http\Dto\EmployeeDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    public function authorize(Request $request): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'integer', 'min:1', 'exists:roles,id'],
            'surname' => ['required', 'string'],
            'name' => ['required', 'string'],
            'patronymic' => ['required', 'string'],
            'email' => [
                'required', 
                'email',
                Rule::unique('employees')->ignore($this->route('employee')),
            ],
            'jobTitle' => ['required', 'string'],
        ];
    }

    public function toDto(): EmployeeDto
    {
        return new EmployeeDto(
            surname: $this->surname,
            name: $this->name,
            patronymic: $this->patronymic,
            email: $this->email,
            jobTitle: $this->jobTitle,
            rolesIds: $this->roles,
            password:null
        );
    }
}
