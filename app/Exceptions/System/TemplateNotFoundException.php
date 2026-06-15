<?php

namespace App\Exceptions\System;

use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Log;

class TemplateNotFoundException extends BaseException
{
    protected int $code = 500;
    public function __construct(
        public string $templatePath
    ){
        parent::__construct('Произошла ошибка при формировании документы');
    }
    public function report():void
    {
        Log::critical('UNEXPECTED: template_not_found',[
            'template_path' => $this->templatePath
        ]);
    }
}
