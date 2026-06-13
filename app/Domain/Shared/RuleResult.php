<?php

namespace App\Domain\Shared;

use App\Enums\AvailabilityCode;
use LogicException;

final readonly  class RuleResult
{
    public function __construct(
        public bool $available,
        public AvailabilityCode | string | null $code = null,
        public string | null $reason = null
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
    ):self
    {
        return new self(
            false,
            $code,
            app(CodeTranslator::class)->translate($code)
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