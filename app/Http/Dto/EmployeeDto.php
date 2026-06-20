<?php

namespace App\Http\Dto;

class EmployeeDto
{
    public function __construct(
        public array $rolesIds,
        public string $surname,
        public string $name,
        public ?string $patronymic,
        public string $email,
        public ?string $password = null,
        public string $jobTitle 
    ){}
}