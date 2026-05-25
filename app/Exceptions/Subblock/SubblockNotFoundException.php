<?php

namespace App\Exceptions\Subblock;

use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Log;

class SubblockNotFoundException extends BaseException
{
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
