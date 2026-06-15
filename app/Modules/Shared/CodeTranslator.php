<?php

namespace App\Modules\Shared;

use App\Enums\AvailabilityCode;

class CodeTranslator{
    public function translate(
        AvailabilityCode | string | null $code
    ):?string {
        if(!$code){
            return null;
        }

        if($code instanceof AvailabilityCode){
            return $this->translationKey($code->value);
        }

        return $this->translationKey($code); 
    }

    protected function translationKey(string $key ):string
    {
        return __("reason_codes.{$key}");
    }
}