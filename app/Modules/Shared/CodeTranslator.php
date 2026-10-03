<?php

namespace App\Modules\Shared;

use App\Enums\BusinessCode;

class CodeTranslator{
    public function translate(
        BusinessCode | null $code,
        array $params = []
    ):?string {
        if(!$code){
            return null;
        }

        if($code instanceof BusinessCode){
            return $this->translationKey($code->value, $params);
        }

        return $this->translationKey($code, $params); 
    }

    protected function translationKey(
        string $key , 
        array $params = []
    ):string {
        return __("business_codes.{$key}", $params);
    }
}