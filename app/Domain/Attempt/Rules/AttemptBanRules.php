<?php

namespace App\Domain\Attempt\Rules;

use App\Domain\Shared\RuleResult;
use App\Models\Attempt;

class AttemptBanRules
{
    public function check(Attempt $attempt):RuleResult
    {
        if($attempt->isBanned()){
            return new RuleResult(
                available: false,
                code: 'attempt_already_banned',
                reason: 'Попытка уже аннулирована'
            );
        }

        if(! $attempt->created_at->isToday()){
            return new RuleResult(
                available: false,
                code: 'attempt_can_be_banned_only_on_attempt_day',
                reason: 'Аннулировать попытку возможно только в день её прохождения'
            );
        }

        return new RuleResult(true);
    }
}