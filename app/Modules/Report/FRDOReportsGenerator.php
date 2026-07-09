<?php

namespace App\Modules\Report;

use App\Enums\ReportType;
use App\Events\ReportGenerated;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

class FRDOReportsGenerator
{
    public function __construct(
        protected EnsureFrdoGenerationAvailable $ensureFrdoGenerationAvailable
    ) {}

    public function execute(
        string $examDate,
        string $type
    ): IWriter {
        $examDate = Carbon::parse($examDate);
        
        $success = $type === 'certificates';

        $this->ensureFrdoGenerationAvailable->execute($examDate, $success);
        $spreadsheet = $this->generateReport($examDate, $success);
        event(new ReportGenerated(ReportType::Frdo, [
            'date' => $examDate,
            'type' => $success ? 'certificates' : 'references'
        ]));

        return IOFactory::createWriter($spreadsheet, 'Xlsx');
    }

    protected function attemptsForReport(
        Carbon $examDate,
        bool $success
    ): Collection {
        $attempts = Attempt::query()
            ->with(['exam.type', 'foreignNational', 'exam.address'])
            ->whereBetween('created_at', [
                $examDate->copy()->startOfDay(),
                $examDate->copy()->endOfDay(),
            ])
            ->when($success, function(Builder $query){
                $query->passed();
            })
            ->when(!$success, function(Builder $query){
                $query->failed();
            })
            ->whereNotNull('checked_at')
            ->get();

        return $attempts;
    }

    protected function generateReport(
        Carbon $examDate,
        bool $success
    ): Spreadsheet {
        $attempts = $this->attemptsForReport($examDate, $success);
        if ($success) {
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
                $lastColumn = $success ? 'O' : 'Q';

                $sheet->duplicateStyle(
                    $sheet->getStyle("A{$templateRow}:{$lastColumn}{$templateRow}"),
                    "A{$row}:{$lastColumn}{$row}"
                );

                $sheet->getRowDimension($row)
                    ->setRowHeight($sheet->getRowDimension($templateRow)->getRowHeight());
            }
            if ($success) {
                $markUp = $this->certificateMarkup($attempt,$row);
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

    protected function certificateMarkup(
        Attempt $attempt,
        int $row
    ): array {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'N', 'O'];
        $values = [
            mb_strtoupper($attempt->foreignNational->surname, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->name, 'UTF-8'),
            mb_strtoupper($attempt->foreignNational->patronymic, 'UTF-8'),
            $attempt->foreignNational->date_birth->format('d.m.Y'),
            $attempt->exam->begin_time->year,
            strtoupper($this->certificateText($attempt->exam->type->certificate_name)),
            strtoupper($attempt->foreignNational->surname_latin),
            strtoupper($attempt->foreignNational->name_latin),
            strtoupper($attempt->foreignNational->patronymic_latin),
            $attempt->foreignNational->full_passport,
            $attempt->foreignNational->citizenship,
            $attempt->exam->address->address,
            $center->certificates_issue_address,
            $center->director_fio,
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
            strtoupper($this->certificateText($attempt->exam->type->certificate_name)),
            strtoupper($attempt->foreignNational->surname_latin),
            strtoupper($attempt->foreignNational->name_latin),
            strtoupper($attempt->foreignNational->patronymic_latin),
            'Справка',
            $attempt->foreignNational->full_passport,
            $attempt->foreignNational->citizenship,
            $attempt->exam->address->address,
            $attempt->exam->begin_time->format('d.m.Y'),
            'Неуспешно',
            $center->director_fio,
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
