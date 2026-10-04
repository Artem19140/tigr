<?php

namespace App\Http\Resources\Center;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CenterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shortName' => $this->short_name,
            'directorFio' => $this->director_fio,
            'certificatesIssueAddress' => $this->certificates_issue_address,
            'ogrn' => $this->ogrn,
            'inn' => $this->inn,
            'registeredAddress' => $this->registered_address,
            'nameGenitive' => $this->name_genitive,
            'commissionChairman' => $this->commission_chairman,
            'timeZone' => $this->time_zone
        ];
    }
}
