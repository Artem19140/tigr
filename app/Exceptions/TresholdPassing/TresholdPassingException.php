<?php

namespace App\Exceptions\TresholdPassing;

use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Log;

class TresholdPassingException extends BaseException 
{
    protected int $code = 500;
    public function __construct(
        public array $context,
        string $message = 'Произошла ошибка при подсчете результов'
    ) {
        parent::__construct($message);
    }

    public function report(): void
    {
        Log::critical('UNEXPECTED: subblock not found', [
            ...$this->context,
        ]);
    }
}
