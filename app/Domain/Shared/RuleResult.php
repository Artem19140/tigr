<?php

namespace App\Domain\Shared;

class RuleResult
{
    public function __construct(
        public bool $available,
        public string | null $code = null,
        public string | null $reason = null
    ){}

    public static function access()
    {
        self::$available = true;
    }

    public static function deny(
        string | null $code = null,
        string | null $reason = null
    )
    {
        self::$code = $code;
        self::$reason = $reason;
    }
}