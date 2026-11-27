<?php

namespace App\Jobs;

use App\Mail\ExportReadyMail;
use App\Models\Attempt;
use App\Services\AnswerGrader;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportStudentResultJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $attemptId) {}

    public function handle(): void
    {
        $attempt = Attempt::with(['exam.questions', 'student'])
            ->findOrFail($this->attemptId);

        $correctCount = $this->computeCorrectCount($attempt);
        $totalCount = $attempt->exam->questions->count();

        // Generate PDF
        $pdf = Pdf::loadView(
            'pdf.student-result',
            compact('attempt', 'correctCount', 'totalCount')
        );

        // Convert PDF into data
        $pdfData = $pdf->output();

        $pdfName = 'result-'.Str::slug($attempt->exam->title).'.pdf';

        // Save PDF in storage
        Storage::disk('local')->put('exports/'.$pdfName, $pdfData);

        // Send PDF through email
        Mail::to($attempt->student->email)->send(
            new ExportReadyMail(
                mailSubject: 'Your result for "'.$attempt->exam->title.'" is ready',
                pdfData: $pdfData,
                pdfName: $pdfName,
            )
        );
    }

    private function computeCorrectCount(Attempt $attempt): int
    {
        $grader = app(AnswerGrader::class);
        $correct = 0;

        foreach ($attempt->exam->questions as $question) {
            if ($grader->isCorrect($question, $attempt->answers[$question->id] ?? null)) {
                $correct++;
            }
        }

        return $correct;
    }
}
