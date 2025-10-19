<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DailyChecklistPdfMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pdfPath;

    public function __construct($pdfPath)
    {
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Daily Operations Checklist')
                    ->view('chl.emails.daily_checklist')  // create this view for email body
                        ->attach(Storage::disk('private')->path($this->pdfPath));
    }
}
