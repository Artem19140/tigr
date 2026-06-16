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
}