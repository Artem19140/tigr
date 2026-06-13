<?php

namespace App\Domain\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Attempt;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ExamProtocolGenerator
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ){}
    public function execute(Exam $exam)
    {
        
        $annulledAttempts = $this->getAnnulledAttempts($exam);
        $beginTimeReal = $this->getBeginTimeReal($exam);
        $endTimeReal = $this->getEndTimeReal($exam);

        $attemptsWithViolations = $exam->attempts()
            ->whereHas('violations')
            ->with(['foreignNational', 'center', 'violations'])
            ->get();

        CenterIsolationCheck::check($attemptsWithViolations);
        CenterIsolationCheck::check($annulledAttempts);

        $pdf = Pdf::loadView('pdf.exam.exam-protocol', [
            'exam' => $exam,
            'center' => $exam->center,
            'annulledAttempts' => $annulledAttempts,
            'beginTimeReal' => $beginTimeReal,
            'endTimeReal' => $endTimeReal,
            'attemptWithViolations' => $attemptsWithViolations,
        ]);
        event(new ExamDocumentGenerated($exam, ExamDocument::Protocol));

        return $pdf;
    }

    protected function getAnnulledAttempts(Exam $exam): Collection
    {
        return Attempt::with('foreignNational')
            ->where('exam_id', $exam->id)
            ->whereNotNull('annulled_at')->get();
    }

    protected function getBeginTimeReal(Exam $exam): ?Carbon
    {
        $min = $exam->attempts()
            ->min('started_at');

        return $min ? Carbon::parse($min, 'UTC')->setTimezone($exam->time_zone) : null;
    }

    protected function getEndTimeReal(Exam $exam): ?Carbon
    {
        $max = $exam->attempts()
            ->max('finished_at');

        return $max ? Carbon::parse($max, 'UTC')->setTimezone($exam->time_zone) : null;
    }
}
