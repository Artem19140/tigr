<?php

namespace App\Modules\Report;

use App\Enums\ReportType;
use App\Events\ReportGenerated;
use App\Models\Attempt;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

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
            CenterData::timeZome()
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
        if ($type === 'certificates') {
            $templatePath = storage_path('app/public/templates/certificates_frdo.xlsx');
        } else {
            $templatePath = storage_path('app/public/templates/references_frdo.xlsx');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $templateRow = 3;
        $row = 3;

        foreach ($attempts as $attempt) {
            if ($row > $templateRow) {
                $lastColumn = $type === 'certificates' ? 'O' : 'Q';

                $sheet->duplicateStyle(
                    $sheet->getStyle("A{$templateRow}:{$lastColumn}{$templateRow}"),
                    "A{$row}:{$lastColumn}{$row}"
                );

                $sheet->getRowDimension($row)
                    ->setRowHeight($sheet->getRowDimension($templateRow)->getRowHeight());
            }

            if ($type === 'certificates') {
                $markUp = $this->certificatesMarkup($attempt,$row);
            } else {
                $markUp = $this->referencesMarkup($attempt, $row);
            }

            foreach ($markUp as $key => $value) {
                $sheet->setCellValue($key, $value);

            }
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

    protected function certificatesMarkup(
        Attempt $attempt,
        int $row
    ): array {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'N', 'O'];
        $values = [
            mb_strtoupper($attempt->foreignNational->surname, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->name, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->patronymic, 'UTF-8'),
            $attempt->foreignNational->date_birth->copy()->format('d.m.Y'),
            $attempt->exam->begin_time->year,
            mb_strtoupper($this->certificateText($attempt->exam->type->certificate_name), 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->surname_latin, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->name_latin, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->patronymic_latin, 'UTF-8'),
            $attempt->foreignNational->full_passport,
            $attempt->foreignNational->citizenship,
            $attempt->exam->address->address,
            $this->center->certificatesIssueAddress(),
            $this->center->directorFio(),
        ];

        $cells = [];
        foreach ($values as $i => $value) {
            $cells[$columns[$i].$row] = $value;
        }

        return $cells;
    }

    protected function referencesMarkup(
        Attempt $attempt,
        int $row
    ): array {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'O', 'P', 'Q'];

        $values = [
            mb_strtoupper($attempt->foreignNational->surname, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->name, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->patronymic, 'UTF-8'),
            $attempt->foreignNational->date_birth->format('d.m.Y'),
            $attempt->started_at->year,
            mb_strtoupper($this->certificateText($attempt->exam->type->certificate_name), 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->surname_latin, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->name_latin, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->patronymic_latin, 'UTF-8'),
            'Справка',
            $attempt->foreignNational->full_passport,
            $attempt->foreignNational->citizenship,
            $attempt->exam->address->address,
            $attempt->exam->begin_time->format('d.m.Y'),
            'Неуспешно',
            $this->center->directorFio(),
        ];
        $cells = [];
        foreach ($values as $index => $value) {
            $cells[$columns[$index].$row] = $value;
        }

        return $cells;
    }

    protected function certificateText(string $certificateName): string
    {
        return "
            Сертификат о владении русским языком, знании истории России 
            и основ законодательства Российской Федерации на уровне, 
            соответствующем цели получения $certificateName
        ";
    }
}