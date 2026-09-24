<?php

namespace App\Enums;

use App\Exceptions\System\TemplateNotFoundException;
use Carbon\Carbon;

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

    public function fileName(
        string $name,
        Carbon $date,
    ): string
    {
         return match($this){
            self::Codes => "Кода_{$name}_{$date->format('H-i_d.m.Y')}.pdf",
            self::List => "Список_{$name}_{$date->format('H-i_d.m.Y')}.pdf", 
            self::Protocol => "Протокол_{$name}_{$date->format('H-i_d.m.Y')}.pdf",
            self::Results => "Результаты_{$name}_{$date->format('H-i_d.m.Y')}.pdf",
            default => throw new TemplateNotFoundException($this->value)
        };
    }
}
