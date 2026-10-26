<?php

namespace App\Jobs;

use App\Mail\ExportReadyMail;
use App\Models\Exam;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ExportExamResultsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $examId,
        public readonly int $teacherId,
    ) {}

    public function handle(): void
    {
        $exam = Exam::with(['attempts' => fn ($q) => $q->completed()->with('student')])->findOrFail($this->examId);
        $teacher = User::findOrFail($this->teacherId);
        $attempts = $exam->attempts;

        $pdf = Pdf::loadView('pdf.exam-results', compact('exam', 'teacher', 'attempts'))
            ->setPaper('a4', 'landscape');
        Mail::to($teacher->email)->send(
            new ExportReadyMail(
                mailSubject: 'Export ready: "'.$exam->title.'" results',
                pdfData: $pdf->output(),
                pdfName: 'results-'.Str::slug($exam->title).'.pdf',
            )
        );
    }
}
