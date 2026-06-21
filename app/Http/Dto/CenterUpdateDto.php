<?php

namespace App\Http\Dto;

final readonly class CenterUpdateDto
{
    public function __construct(
        public string $name,
        public string $directorFio,
        public string $certificatesIssueAddress,
        public string $ogrn,
        public string $inn,
        public string $address,
        public string $nameGenitive,
        public string $commissionChairman,
    ){}

    public function toArray(): array 
    {
        return [
            'name' => $this->name,
            'director_fio' => $this->directorFio,
            'certificates_issue_address' => $this->certificatesIssueAddress,
            'ogrn' => $this->ogrn,
            'inn' => $this->inn,
            'address' => $this->address,
            'name_genitive' => $this->nameGenitive,
            'commission_chairman' => $this->commissionChairman,
        ];
    }
}