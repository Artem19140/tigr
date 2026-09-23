<?php

namespace App\Http\Resources\ForeignNational;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForeignNationalEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,

            'surname' => $this->resource->surname,
            'name' => $this->resource->name,
            'patronymic' => $this->resource->patronymic,

            'surnameLatin' => $this->resource->surname_latin,
            'nameLatin' => $this->resource->name_latin,
            'patronymicLatin' => $this->resource->patronymic_latin,

            'dateBirth' => $this->resource->date_birth,
            'citizenship' => $this->resource->citizenship,
            
            'passportSeries' => $this->resource->passport_series,
            'passportNumber' => $this->resource->passport_number,
            'issuedBy' => $this->resource->issued_by,
            'issuedDate' => $this->issued_date,

            'phone' => $this->resource->phone ?? null,
            'addressReg' => $this->address_reg,
            'comment' =>  $this->comment,
            'gender' => $this->gender,

            'fullName' => $this->full_name,
            'fullPassport' => $this->full_passport,
        ];
    }
}
