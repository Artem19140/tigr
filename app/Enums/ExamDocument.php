<?php

namespace App\Enums;

use App\Exceptions\System\TemplateNotFoundException;

enum ExamDocument: string
{
    case List = 'list';
    case Codes = 'codes';
    case Protocol = 'protocol';
    case Results = 'results';

    public  function templatePath():string
    {
        return match($this){
            self::Codes => 'pdf.exam.codes',
            self::List => 'pdf.exam.foreign_nationals-list', 
            self::Protocol => 'pdf.exam.protocol',
            self::Results => 'pdf.exam.results',
            default => throw new TemplateNotFoundException($this->value)
        };
    }
}
