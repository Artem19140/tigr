<?php

namespace App\Exceptions\Counter;

use App\Enums\CounterKey;
use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Log;

class CounterNotFoundException extends BaseException
{
    protected int $code = 500;
    public function __construct(
        public CounterKey $counter,
        string $message = 'Запрашиваемые данные не найдены'
    ) {
        parent::__construct($message);
    }

    public function report(): void
    {
        Log::critical('counter_not_found', [
            'counter' => $this->counter,
        ]);
    }
}
