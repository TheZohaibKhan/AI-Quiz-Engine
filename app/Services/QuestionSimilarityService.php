<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class QuestionSimilarityService
{
    /**
     * @return Collection<int, Question>
     */
    public function findSimilar(
        string $query,
        int $limit = 5,
        ?int $excludeExamId = null
    ): Collection {
        $questions = Question::query()
            ->whereNotNull('embedding')
            ->when(
                $excludeExamId,
                fn ($q) => $q->where('exam_id', '!=', $excludeExamId)
            );

        if (DB::connection()->getDriverName() === 'pgsql') {
            return $questions
                ->whereNotNull('embedding')
                ->whereVectorSimilarTo(
                    'embedding',
                    $query,
                    minSimilarity: 0.3
                )
                ->limit($limit)
                ->get();
        }

        // SQLite fallback: filter decoded embedding values.
        return $questions
            ->get()
            ->filter(fn (Question $question) => is_array($question->embedding)
                && $question->embedding !== []
            )
            ->take($limit)
            ->values();
    }
}
