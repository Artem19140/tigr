<?php

namespace App\Modules\Scheduler;

use App\Models\Enrollment;
use App\Support\Audit;
use Carbon\Carbon;

final class ClearExpiredExamCodes
{
    public function __construct(
        protected Audit $audit
    ){}
    public function execute(): void
    {

        $count = Enrollment::where('exam_code_expired_at', '<', Carbon::now())
            ->whereNotNull('exam_code')
            ->update(['exam_code' => null]);

        if ($count > 0) {
            $this->audit->log(
                'deleted_exam_codes',
                '',
                [
                    'count' => $count,
                    'actor' => 'cron',
                ]
            );
        }
    }
}
