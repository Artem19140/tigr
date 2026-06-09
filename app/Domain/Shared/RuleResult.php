<?php

namespace App\Domain\Shared;

class RuleResult
{
    public function __construct(
        public bool $available,
        public string | null $code = null,
        public string | null $reason = null
    ){}

    // public static function access()
    // {
    //     return self::__construct(
    //         true
    //     ) ;
    // }

    // public static function deny(
    //     string | null $code = null,
    //     string | null $reason = null
    // )
    // {
    //     return self::(
    //         false,
    //         $code,
    //         $reason
    //     ) ;
    // }
}