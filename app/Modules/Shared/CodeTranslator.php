<?php

namespace App\Modules\Shared;

use App\Enums\AvailabilityCode;

class CodeTranslator{
    public function translate(
        AvailabilityCode | string | null $code,
        array $params = []
    ):?string {
        if(!$code){
            return null;
        }

        if($code instanceof AvailabilityCode){
            return $this->translationKey($code->value, $params);
        }

        return $this->translationKey($code, $params); 
    }

    protected function translationKey(string $key , array $params = []):string
    {
        return __("reason_codes.{$key}", $params);
    }
}