<?php

namespace App\Enums;

enum TaskType: string
{
    case SingleChoice = 'single-choice';
    case TextInput = 'text-input';
    case Essay = 'essay';
    case MultyInput = 'multy-input';
    case Speaking = 'speaking';

    public function autoCheck(): bool
    {
        return match ($this) {
            self::SingleChoice => true,
            self::TextInput => true,
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

    public static function autoCheckTypes(): array
    {
        return array_map(
            fn ($case) => $case->value,
            array_filter(self::cases(), fn ($case) => $case->autoCheck())
        );
    }

    public static function manualCheckTypes(): array
    {
        return array_map(
            fn ($case) => $case->value,
            array_filter(self::cases(), fn ($case) => ! $case->autoCheck())
        );
    }
}
