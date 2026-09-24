<?php

namespace App\Enums;

enum TaskType: string
{
    case SingleChoice = 'single-choice';
    case SingleInput = 'single-input';
    case Essay = 'essay';
    case MultyInput = 'multy-input';
    case Speaking = 'speaking';

    public function autoReview(): bool
    {
        return match ($this) {
            self::SingleChoice => true,
            default => false
        };
    }

    public function hasAnswers(): bool
    {
        return match ($this) {
            self::Speaking => false,
            default => true
        };
    }

    public static function autoReviewTypes(): array
    {
        return array_map(
            fn ($case) => $case->value,
            array_filter(self::cases(), fn ($case) => $case->autoReview())
        );
    }

    public static function manualReviewTypes(): array
    {
        return array_map(
            fn ($case) => $case->value,
            array_filter(self::cases(), fn ($case) => ! $case->autoReview())
        );
    }
}
