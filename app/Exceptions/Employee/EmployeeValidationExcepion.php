<?php

namespace App\Exceptions\Employee;

use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Log;

class EmployeeValidationExcepion extends BaseException
{
    protected $busCode = 'employee_saving_error';
    public function __construct(
        public string $logMessage,
        public array $context
    ){
        parent::__construct('Произошла ошибка при сохранении сотрудника');
    }
    public function report(): void
    {
        Log::critical($this->logMessage, $this->context);
    }
}
