<?php

namespace App\Support;

use App\Modules\Shared\ExamSettings;

class CodeGenerator
{
    public function generate(): string {
        $length = ExamSettings::codesLength();
        $max = (10 ** $length) - 1;
        $rnd = random_int(0, $max);
        $code = str_pad($rnd, $length, '0', STR_PAD_LEFT);

        return $code;
    }
    
}