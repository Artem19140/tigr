<?php

namespace App\Modules\Shared;

use App\Enums\BusinessCode;

final readonly class RuleResult
{
    protected function __construct(
        public bool $available,
        protected BusinessCode | null $code = null,
        protected array $params = []
    ){}

    public static function success():self
    {
        return new self(true);
    }

    public static function fail(
        BusinessCode | string $code,
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

    public function code(): BusinessCode | null
    {        
        return $this->code;
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