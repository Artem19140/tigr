<?php

namespace App\Modules\ExamDocument;

use App\Models\Exam;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StatementXlsxGenerator{
    public function __construct(
        private ExamStatementDataBuilder $builder
    ){}
    public function generate(
        Exam $exam 
    ){
        $build = $this->builder->build($exam);

        $templatePath = $this->resolveTemplatePath($exam);
        
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet = $this->replaceVariables(
            $sheet,
            $this->getVariables($exam)
        );

        $sheet = $this->fillTable(
            $sheet,
            $build['rows']
        );

        $sheet = $this->fillTesters(
            $sheet,
            $exam->examiners->pluck('full_name')->toArray()
        );

        return IOFactory::createWriter($spreadsheet, 'Xlsx');
    }

    protected function resolveTemplatePath(
        Exam $exam
    ){
        $path =  match ($exam->level()) {
            1 => 'documents_templates/statement_patent_v1.xlsx'
        };

        if(! Storage::disk('local')->exists($path)){
            Log::warning('Template xlsx for exam not found', [
                'exam' => $exam->id
            ]);
            
            abort(400, 'Ошибка при генерации документа');

            
        }

        return Storage::disk('local')->path($path) ;
    }

    protected function replaceVariables(
        Worksheet $sheet,
        array $variables
    ):  Worksheet {
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $value = $cell->getValue();

                if (\is_string($value)) {
                    $cell->setValue(
                        str_replace(
                            array_keys($variables),
                            array_values($variables),
                            $value
                        )
                    );
                }
            }
        }
        return $sheet;
    }

    protected function getVariables(
        Exam $exam
    ): array {
        return [
            '{{address}}' => $exam->addressName(),
            '{{session}}' => $exam->session,
            '{{group}}' => $exam->group,
            '{{date}}' => $exam->beginTimeLocal()->format('d.m.Y')
        ];
    }

    protected function fillTable(
        Worksheet $sheet,
        array $rows
    ): Worksheet {
        $lineNumber = 6;
        $sequenceNumber = 1;
        
        foreach($rows as $row){
            if ($sequenceNumber > 1) {
                $this->setRowStyles($sheet, $lineNumber);
            }

            $columnNumber = 1; 

            $sheet->setCellValue([
                $columnNumber, $lineNumber
            ], $sequenceNumber);

            $columnNumber++;

            foreach($row as $data){

                $sheet->setCellValue([
                    $columnNumber, $lineNumber
                ], $data['value']);

                $columnNumber ++;
            }
            
            $sequenceNumber ++;
            $lineNumber ++;
        }
        
        return $sheet;
    }

    protected function setRowStyles(
        Worksheet $sheet,
        int $lineNumber
    )
    {
        $lastColumn = $sheet->getHighestDataColumn();
        $previousRow = $lineNumber - 1;

        for ($column = 1; $column <= Coordinate::columnIndexFromString($lastColumn); $column++) {
            $columnLetter = Coordinate::stringFromColumnIndex($column);

            $source = "{$columnLetter}{$previousRow}";
            $target = "{$columnLetter}{$lineNumber}";

            $sheet->duplicateStyle(
                $sheet->getStyle($source),
                $target
            );
        }
    }

    protected function fillTesters(
        Worksheet $sheet,
        array $names
    ){
        $lastRow = $sheet->getHighestRow();

        $sheet->setCellValue(
            [ 2, $lastRow + 2], 
            'Ответственные по проведению экзамена (тесторы):'
        );

        $i = 1;

        foreach ($names as $name) {
            $row = $lastRow + 2 + $i;

            $sheet->setCellValue([2, $row], $name );
            
            $sheet->getStyle([3, $row])
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(Border::BORDER_THIN);
            $i++;
        }
        

        return $sheet;
    }
}