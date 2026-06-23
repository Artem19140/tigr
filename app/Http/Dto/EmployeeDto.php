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
        public string $jobTitle 
    ){}

    public function toArray(): array
    {
        return [
            'surname' => $this->surname,
            'name' => $this->name,
            'patronymic' => $this->patronymic,
            'email' => $this->email,
            'job_title' => $this->jobTitle
        ];
    }
}