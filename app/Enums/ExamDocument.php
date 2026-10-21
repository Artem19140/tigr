<?php

namespace App\Enums;

use Carbon\Carbon;

enum ExamDocument: string
{
    case List = 'list';
    case Codes = 'codes';
    case Protocol = 'protocol';
    case Results = 'results';
    case Scores = 'scores';
    case Statement = 'statement';

    public  function templatePath():string
    {
        return match($this){
            self::Codes => 'pdf.exam.codes',
            self::List => 'pdf.exam.foreign_nationals-list', 
            self::Protocol => 'pdf.exam.protocol',
            self::Results => 'pdf.exam.results',
            self::Scores => 'pdf.exam.scores',
            self::Statement => 'pdf.exam.statement'
        };
    }

    public function fileName(
        string $examName,
        Carbon $date,
        string | null $extension = 'pdf'
    ): string
    {
         return match($this){
            self::Codes => "Кода_{$examName}_{$date->format('H-i_d.m.Y')}.$extension",
            self::List => "Список_{$examName}_{$date->format('H-i_d.m.Y')}.$extension", 
            self::Protocol => "Протокол_{$examName}_{$date->format('H-i_d.m.Y')}.$extension",
            self::Results => "Результаты_{$examName}_{$date->format('H-i_d.m.Y')}.$extension",
            self::Scores => "Баллы_{$examName}_{$date->format('H-i_d.m.Y')}.$extension",
            self::Statement => "Ведомость_{$examName}_{$date->format('H-i_d.m.Y')}.$extension",
        };
    }
}
