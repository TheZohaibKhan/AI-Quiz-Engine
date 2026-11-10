<?php

namespace App\Jobs;

use App\Models\Question;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\Ai\Embeddings;

class GenerateQuestionEmbeddingJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public Question $question) {}

    public function handle(): void
    {
        // Do not generate embeddings when using the sync queue.
        if (config('queue.default') === 'sync') {
            return;
        }

        $text = $this->question->question.' '.$this->question->correct_answer;

        $response = Embeddings::for([$text])->generate();

        $embedding = $response->embeddings[0] ?? null;

        if ($embedding === null) {
            throw new \RuntimeException(
                'Question embedding generation returned no result.'
            );
        }

        $this->question->update([
            'embedding' => $embedding,
        ]);
    }
}
