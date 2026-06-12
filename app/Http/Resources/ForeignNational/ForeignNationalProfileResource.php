<?php

namespace App\Http\Resources\ForeignNational;

use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Enrollment\EnrollmentResource;
use App\Http\Resources\Document\DocumentResource;
use App\Models\Document;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForeignNationalProfileResource extends JsonResource
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
            'dateBirth' => $this->resource->date_birth,
            'surnameLatin' => $this->surname_latin,
            'comment' => $this->comment,
            'surname' => $this->resource->surname,
            'name' => $this->resource->name,
            'patronymic' => $this->resource->patronymic,
            'nameLatin' => $this->resource->name_latin,
            'patronymicLatin' => $this->resource->patronymic_latin,
            'passportNumber' => $this->resource->passport_number,
            'passportSeries' => $this->resource->passport_series,
            'issuedBy' => $this->resource->issued_by,
            'issuedDate' => $this->issued_date,
            'citizenship' => $this->resource->citizenship,
            'phone' => $this->resource->phone,
            'gender' => $this->resource->gender,
            'creator' => new EmployeeResource($this->whenLoaded('creator')),
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'passport' => $this->passport,
            'passportTranslate' => $this->passport_translate,
            'createdAt' => $this->created_at,
            'fullName' => $this->full_name,
            'fullNameLatin' => $this->full_name_latin,
            'fullPassport' => $this->full_passport,
            'creatorFullName' => $this->whenLoaded('creator', fn () => $this->creator->full_name),
            'addressReg' => $this->address_reg,
            'documents' =>  DocumentResource::collection($this->whenLoaded('documents')),
            'permissions' => [
                'enroll' => $employee->can('create', Enrollment::class),
                'edit' => $employee->can('update', $this->resource),
                'documents' => $employee->can('viewAny', Document::class),
                'enrollments' => $employee->can('viewAny', Enrollment::class)
            ],
        ];
    }
}
