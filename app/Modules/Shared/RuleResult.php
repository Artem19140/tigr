<?php

namespace App\Modules\Shared;

use App\Enums\AvailabilityCode;
use LogicException;

final readonly  class RuleResult
{
    protected function __construct(
        public bool $available,
        protected AvailabilityCode | string | null $code = null,
        protected string | null $reason = null
    ){ 
        if(! $available && ! $code){
            throw new LogicException('UNEXPECTED: availalble = false, and no code');
        }
    }

    public static function success():self
    {
        return new self(true);
    }

    public static function fail(
        AvailabilityCode | string $code,
        string | null $reason = null
    ):self
    {
        return new self(
            false,
            $code,
            $reason ?? app(CodeTranslator::class)->translate($code)
        );
    }

    public function reason(): ?string
    {
        return $this->reason; 
    }

    public function isNotAvailable():bool
    {
        return ! $this->available;
    }
}