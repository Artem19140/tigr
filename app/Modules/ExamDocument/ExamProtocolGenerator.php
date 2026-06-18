<?php

namespace App\Modules\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Violation;
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
        $attemptsWithViolations = $this->getAttemptsWithViolations($exam);

        CenterIsolationCheck::check($attemptsWithViolations);
        CenterIsolationCheck::check($annulledAttempts);

        $pdf = Pdf::loadView(ExamDocument::Protocol->templatePath(), [
            'exam' => $exam,
            'center' => $exam->center,
            'annulledAttempts' => $annulledAttempts,
            'beginTimeReal' => $beginTimeReal,
            'endTimeReal' => $endTimeReal,
            'attemptWithViolations' => $attemptsWithViolations,
        ]);
        
        event(new ExamDocumentGenerated($exam, ExamDocument::Protocol,[
            'annulled_attempts_count' => $annulledAttempts->count(),
            'begin_time_real' => $beginTimeReal?->format('H:i'),
            'end_time_real' => $endTimeReal->format('H:i'),
            'attempt_with_violations_count' => $attemptsWithViolations->count()
        ]));

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

    protected function getAttemptsWithViolations(Exam $exam): Collection
    {
        $attempts = $exam->attempts()
            ->whereHas('violations')
            ->with(['foreignNational', 'center', 'violations'])
            ->get();

        $attempts->each(function(Attempt $attempt){
            $attempt->violations->each(function(Violation $violation)use($attempt){
                return $violation->setRelation('attempt', $attempt);
            });
        });
        return $attempts;
    }
}
