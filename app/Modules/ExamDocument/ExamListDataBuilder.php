<?php

namespace App\Modules\ExamDocument;

use App\Models\Exam;

class ExamListDataBuilder
{
    public function build(
        Exam $exam
    ): array{
        return [
            'headers' => $this->headers(),
            'rows' => $this->rows($exam)
        ];
    }

    protected function headers():array
    {
        return [
            'fullName' => 'ФИО',
            'fullNameLatin' => 'ФИО (лат.)',
            'fullPassport' => 'Паспорт',
            'Дата рождения',
            'Гражданство'
        ];
    }

    protected function rows(
        Exam $exam
    ): array {
        $rows = [];

        foreach($exam->enrollments as $enrollment){
            $rows[] = [ 
                'fullName' => $enrollment->foreignNational->full_name,
                'fullNameLatin' => $enrollment->foreignNational->full_name_latin,
                'fullPassport' =>  $enrollment->foreignNational->full_passport,
                'dateBirth' => $enrollment->foreignNational->formattedDateBirth(),
                'citizenship' => $enrollment->foreignNational->citizenship
            ];

        }

        return $rows;
    }
    
    
}