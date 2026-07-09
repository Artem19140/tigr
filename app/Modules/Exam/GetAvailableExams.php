<?php

namespace App\Modules\Exam;

use App\Models\Exam;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GetAvailableExams
{
    public function execute(
        int $examTypeId,
        ?int $foreignNationalId = null
    ): Collection {
        $enrollmentCloseBeforeMinutes = ExamSettings::enrollmentCloseBeforeExamMinutes();
        $exams = Exam::select('id', 'begin_time')
            ->where('exam_type_id', $examTypeId)
            ->notCancelled()
            ->where('begin_time', '>', Carbon::now()->addMinutes($enrollmentCloseBeforeMinutes))
            ->when($foreignNationalId, function (Builder $query) use ($foreignNationalId) {
                $query->whereDoesntHave('enrollments', function (Builder $q) use ($foreignNationalId) {
                    $q->where('foreign_national_id', $foreignNationalId);
                });
            })
            ->whereHas('enrollments', function (Builder $q) {}, '<', DB::raw('exams.capacity'))
            ->orderBy('begin_time')
            ->limit(10)
            ->get();

        return $exams;
    }
}
