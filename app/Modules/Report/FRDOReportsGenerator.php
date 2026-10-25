<?php

namespace App\Modules\Report;

use App\Enums\ReportType;
use App\Events\ReportGenerated;
use App\Models\Attempt;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class FRDOReportsGenerator
{
    public function __construct(
        protected CenterData $center 
    ) {}

    public function execute(
        string $date,
        string $type
    ): IWriter {
        $date = Carbon::parse($date)->setTimezone(
            CenterData::timeZone()
        );
    
        $spreadsheet = $this->generateReport(
            $date, 
            $type
        );
        
        event(new ReportGenerated(ReportType::Frdo, [
            'date' => $date->copy()->format('d.m.Y'),
            'type' => $type
        ]));

        return IOFactory::createWriter($spreadsheet, 'Xlsx');
    }

    protected function generateReport(
        Carbon $date,
        string $type
    ): Spreadsheet {
        $attempts = $this->attemptsForReport($date, $type);
        
        $templatePath = $this->getTemplatePath($type);
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $row = 3;

        foreach ($attempts as $attempt) {

            if($row > 3){
                $sheet = $this->setRowStyles(
                    $sheet,
                    $row
                );
            }
            $sheet->fromArray(
                $this->getRow($attempt, $type),
                null,
                "A$row"
            );

            $row++;
        }

        return $spreadsheet;
    }

    protected function attemptsForReport(
        Carbon $date,
        string $type
    ): Collection {
        $attempts = Attempt::query()
            ->with([
                'exam.type', 
                'foreignNational', 
                'exam.address'
            ])
            ->whereBetween('created_at', [
                $date->copy()->startOfDay()->utc(),
                $date->copy()->endOfDay()->utc(),
            ])
            ->when($type === 'certificates', function(Builder $query){
                $query->passed();
            })
            ->when($type === 'references', function(Builder $query){
                $query->failed();
            })
            ->whereNotNull('reviewed_at')
            ->get();

        return $attempts;
    }
    protected function certificateText(string $certificateName): string
    {
        return "Сертификат о владении русским языком, знании истории России и основ законодательства Российской Федерации на уровне, соответствующем цели получения $certificateName";
    }

    protected function getTemplatePath(
        string $type
    ): string {
        $path = "templates/{$type}_frdo.xlsx";

        if(! Storage::disk('public')->exists($path)){
            Log::warning("Template for {$type} does not exist");
            abort(500, 'Ошибка при генерации отчета');
        }

        return Storage::disk('public')->path($path);        
    }

    protected function upperCase(string | null $string){
        return mb_strtoupper($string ?? '', 'UTF-8');
    }

    protected function getRow(
        Attempt $attempt,
        string $type
    ): array {
        $values = [
            $this->upperCase($attempt->foreignNational->surname),
            $this->upperCase($attempt->foreignNational->name),
            $this->upperCase($attempt->foreignNational->patronymic),
            $attempt->foreignNational->formattedDateBirth(),
            $attempt->started_at->year,
            $this->certificateText($attempt->exam->certificateName()),
            $this->upperCase($attempt->foreignNational->surname_latin),
            $this->upperCase($attempt->foreignNational->name_latin),
            $this->upperCase($attempt->foreignNational->patronymic_latin),
        ];

        if($type === 'references'){
            $values[] = 'СПРАВКА';
        }

        $values = [
            ...$values,
            $this->upperCase($attempt->foreignNational->full_passport),
            $this->upperCase($attempt->foreignNational->citizenship),
            '',
            $this->center->certificatesIssueAddress()
        ];

        if($type === 'references'){
            $values = [
                ...$values,
                $attempt->exam->begin_time->format('d.m.Y'),
                'Неуспешно',
            ];
        }else{
            $values[] = $this->center->certificatesIssueAddress();
        }

        $values[] = $this->center->directorFio();

        return $values;

    }

    protected function setRowStyles(
        Worksheet $sheet,
        int $lineNumber
    ): Worksheet{
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
        return $sheet;
    }
}