<?php

namespace App\Modules\Shared;

use App\Enums\AvailabilityCode;

final readonly  class RuleResult
{
    
    protected function __construct(
        public bool $available,
        protected AvailabilityCode | string | null $code = null,
        protected array $params = []
    ){}

    public static function success():self
    {
        return new self(true);
    }

    public static function fail(
        AvailabilityCode | string $code,
        array $params = []
    ):self
    {
        return new self(
            false,
            $code,
            $params
        );
    }

    public function message(): ?string
    {
        return app(CodeTranslator::class)->translate($this->code, $this->params); 
    }

    public function isNotAvailable():bool
    {
        return ! $this->available;
    }

    public function toArray(): array
    {
        return [
            'available' => $this->available,
            'code' => $this->code,
            'reason' => $this->message()
        ];
    }
}