<?php

namespace App\Models;

use App\Enums\QuestionType;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'order',
        'embedding',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'options' => 'array',
            'order' => 'integer',
            'embedding' => 'array',
        ];
    }

    protected static function booted(): void
    {
        // Safety net: if the correct answer is a letter (A, B, C...) and the question
        // has options, store the matching option text so grading always works.
        static::saving(function (Question $question): void {
            if ($question->type === QuestionType::Coding) {
                return;
            }

            $options = $question->options;
            $answer = trim((string) $question->correct_answer);

            if (! is_array($options) || $options === [] || in_array($answer, $options, true)) {
                return;
            }

            if (preg_match('/^\(?([A-Za-z])[\).]?$/', $answer, $m)) {
                $index = ord(strtoupper($m[1])) - ord('A');

                if (isset($options[$index])) {
                    $question->correct_answer = $options[$index];
                }
            }
        });
    }

    /**
     * Settings of a coding question: languages, test cases, html_reference.
     * They live in the existing `options` JSON column, so no database change is needed.
     *
     * @return array{languages: array<int, string>, tests: array<int, array{input: string, output: string}>, html_reference: string}|null
     */
    public function codingConfig(): ?array
    {
        if ($this->type !== QuestionType::Coding) {
            return null;
        }

        $o = is_array($this->options) ? $this->options : [];

        return [
            'languages' => array_values($o['languages'] ?? ['python']),
            'tests' => array_values($o['tests'] ?? []),
            'html_reference' => (string) ($o['html_reference'] ?? ''),
        ];
    }

    /** Answer choices (only multiple choice / true-false have them). */
    public function choices(): array
    {
        return $this->type->hasOptions() && is_array($this->options) ? $this->options : [];
    }

    /** Short answers whose expected answer is code get a bigger, monospace box. */
    public function expectsCode(): bool
    {
        return $this->type === QuestionType::ShortAnswer
            && (bool) preg_match('/[<>{};=]/', (string) $this->correct_answer);
    }

    /** @return BelongsTo<Exam, $this> */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
