<?php

namespace App\Enums;

enum QuestionType: string
{
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case ShortAnswer = 'short_answer';
    case Coding = 'coding';

    public function label(): string
    {
        return match ($this) {
            QuestionType::MultipleChoice => 'Multiple Choice',
            QuestionType::TrueFalse => 'True / False',
            QuestionType::ShortAnswer => 'Short Answer',
            QuestionType::Coding => 'Coding (compiler)',
        };
    }

    public function hasOptions(): bool
    {
        return $this === QuestionType::MultipleChoice || $this === QuestionType::TrueFalse;
    }

    /** Types the AI question generator is allowed to create. */
    public static function generatable(): array
    {
        return [self::MultipleChoice, self::TrueFalse, self::ShortAnswer];
    }
}
