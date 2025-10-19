<?php

namespace App\Jobs;

use App\Mail\MdsAdminCancelBookingMail;
use App\Models\GeneralSettings\MailConfig;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendAdminCancelBookingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function handle()
    {
        // $mail_config = Cache::remember('mail_config', 60*60, function () {
        //     return MailConfig::first();
        // });

        // $mailer = Mail::build([
        //     'transport' => $mail_config->transport,// 'smtp',
        //     'host' => $mail_config->host, //'smtp.gmail.com',
        //     'port' => $mail_config->port, //'465',
        //     'from' => [
        //         'address' => $mail_config->from_address, // 'mds@example.com',
        //         'name' => $mail_config->from_name, //'MDS',
        //     ],
        //     'encryption' => $mail_config->encryption, //'tls',
        //     'username' => $mail_config->username, // 'rsabha@gmail.com',
        //     'password' => $mail_config->password, // 'eploqntpgghoiofx',
        // ]);

        // $mailer->to($this->details['email'])->send(new MdsAdminCancelBookingMail($this->details));
        Mail::to($this->details['email'])->send(new MdsAdminCancelBookingMail($this->details));
    }
}
