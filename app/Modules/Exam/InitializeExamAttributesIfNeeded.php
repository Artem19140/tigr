<?php

namespace App\Modules\Exam;

use App\Models\Exam;
use App\Modules\Counter\GroupNumberGenerator;
use App\Modules\Counter\SessionNumberGenerator;

class InitializeExamAttributesIfNeeded
{
    public function __construct(
        protected GroupNumberGenerator $groupNumberGenerator,
        protected SessionNumberGenerator $sessionNumberGenerator
    ) {}
    public function execute(Exam $exam)
    {
        $needSave = false;

        if (! $exam->group) {
            $needSave = true;
            $exam->group = $this->groupNumberGenerator->execute();
        }
        if (! $exam->session) {
            $needSave = true;
            $exam->session = $this->sessionNumberGenerator->execute();
        }
        
        if ($needSave) {
            $exam->save();
        }
    }
}