<?php

namespace App\Modules\AttemptAnswer\Handlers;

use App\Enums\TaskType;
use App\Exceptions\AttemptAnswerValidationException;
use App\Models\AttemptAnswer;

class MultyInputTaskHandler
{
    public function for($task): bool
    {
        return $task->type === TaskType::MultyInput;
    }

    public function validate(
        mixed $foreignNationalAnswer,
        AttemptAnswer $attemptAnswer
    ): array {
        if(! \is_array($foreignNationalAnswer)){
            throw new AttemptAnswerValidationException([
                'type' => TaskType::MultyInput->value,
                'message' => 'answer not array',
                'foreign_national_answer' => $foreignNationalAnswer,
                'attempt_answer_id' => $attemptAnswer->id
            ]);
        }
        $etalonAnswers = $this->getEtalonAnswers($attemptAnswer);

        $normalizedEtalonKeys = $this->getAndNormalizeKeys($etalonAnswers);
        $normalizedForeignKeys = $this->getAndNormalizeKeys($foreignNationalAnswer);

        $this->ensureMatchingKeys(
            $normalizedForeignKeys,
            $normalizedEtalonKeys,
            $attemptAnswer
        );

        return $foreignNationalAnswer;
    }

    protected function getEtalonAnswers(AttemptAnswer $attemptAnswer)
    {
        $content = $attemptAnswer->taskVariant->content;
        $keys = [];
        $this->recurse($content, $keys);
    
        return $keys;
    }

    protected function recurse(mixed $node, array &$fields): void
    {
        if (! \is_array($node)) {
            return;
        }

        if (isset($node['field_id'])) {
            $fields[$node['field_id']] = '';
        }

        foreach ($node as $child) {
            $this->recurse($child, $fields);
        }
    }

    protected function getAndNormalizeKeys(array $answers): array
    {
        $keys = array_keys($answers);
        sort($keys);

        return $keys;
    }

    protected function ensureMatchingKeys(
        array $normalizedForeignKeys,
        array $normalizedEtalonKeys,
        AttemptAnswer $attemptAnswer
    ): void {
        if ($normalizedEtalonKeys !== $normalizedForeignKeys) {
            throw new AttemptAnswerValidationException([
                'type' => TaskType::MultyInput->value,
                'message' => 'errorValidation',
                'normalized_foreign_keys' => $normalizedForeignKeys,
                'normalized_etalon_keys' => $normalizedEtalonKeys,
                'attempt_answer_id' => $attemptAnswer->id
            ]);
        }
    }
}
