<?php

namespace App\Exceptions;

use App\Enums\BusinessCode;
  
class BusinessException extends \Exception {
    public function __construct(
        public BusinessCode  $reasonCode,
        public array $params = []
    ){
        parent::__construct($reasonCode->value);
    }
}
