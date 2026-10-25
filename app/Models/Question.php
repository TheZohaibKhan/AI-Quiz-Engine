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

    /** @return BelongsTo<Exam, $this> */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
