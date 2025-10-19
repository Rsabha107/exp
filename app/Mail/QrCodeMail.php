<?php

namespace App\Mail;

use App\Models\Sps\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeMail extends Mailable 
{
     //, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $profile;
    public $qrBase64;
    public $qrUrl;

    public function __construct(Profile $profile, public $qrFilePath, public $qrFilePathM)
    {
        $this->profile = $profile;


        // $this->qrBase64 = base64_encode(QrCode::format('png')->size(200)->generate($qrUrl));
    }


    public function build()
    {
        return $this->subject('Your Profile Confirmation')
            ->view('sps.emails.profile_confirmation')
            ->attach(
                $this->qrFilePath,
                [
                    'as' => 'profile-confirmation' . $this->profile->ref_number . '.png',
                    'mime' => 'image/png',
                ]
            )
            ->attach(
                $this->qrFilePathM,
                [
                    'as' => 'profile-confirmation-m' . $this->profile->ref_number . '.png',
                    'mime' => 'image/png',
                ]
            );

        // return $this->subject('Your Profile Confirmation')
        //             ->view('sps.emails.profile_confirmation');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
