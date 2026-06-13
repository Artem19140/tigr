<?php

namespace App\Domain\Shared;

final readonly class RuleResult
{
    public function __construct(
        public bool $available,
        public string | null $code = null,
        public string | null $reason = null
    ){}

    public static function success():self
    {
        return new self(true);
    }

    public static function fail(
        string | null $code = null,
        string | null $reason = null
    ):self
    {
        return new self(
            false,
            $code,
            $reason
        );
    }

    public function isNotAvailable():bool
    {
        return ! $this->available;
    }
}