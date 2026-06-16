<?php

namespace App\Http\Requests\Center;

use App\Http\Dto\CenterUpdateDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CenterUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => ['required', 'string'],
            'directorFio' => ['required', 'string'],
            'certificatesIssueAddress' => ['required', 'string'],
            'ogrn' => ['required', 'string'],
            'inn' => ['required', 'string'],
            'address' => ['required', 'string'],
            'nameGenitive' => ['required', 'string'],
            'commissionChairman' => ['required', 'string'],
        ];
    }

    public function dto(): CenterUpdateDto
    {
        return new CenterUpdateDto(
            name: $this->input('name'),
            directorFio: $this->input('directorFio'),
            certificatesIssueAddress: $this->input('certificatesIssueAddress'),
            ogrn: $this->input('ogrn'),
            inn: $this->input('inn'),
            address: $this->input('address'),
            nameGenitive: $this->input('nameGenitive'),
            commissionChairman: $this->input('commissionChairman'),
        );
    }
}
