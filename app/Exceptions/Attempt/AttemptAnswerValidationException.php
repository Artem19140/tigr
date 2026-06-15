<?php

namespace App\Exceptions\Attempt;

use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Log;

class AttemptAnswerValidationException extends BaseException
{
    protected int $code = 500;
    public function __construct(
        public array $context,
        string $message = 'Произошла ошибка при обработке ответа'
    ) {
        parent::__construct($message);
    }

    public function report(): void
    {
        Log::critical('UNEXPECTED: attempt answer validation failed', $this->context);
    }
}
