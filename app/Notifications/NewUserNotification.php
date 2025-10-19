<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $user_detail;
    public function __construct($user_detail)
    {
        //
        $this->user_detail = $user_detail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->greeting('Hello '.$this->user_detail['name'])
                    ->subject('Welcome to our application')
                    ->line('Please activate your account by clicking the link below.')
                    ->line('Your will be asked to change your password after activation.')
                    ->line('Your email is: '.$this->user_detail['email'])
                    ->line('Your token is: '.$this->user_detail['token'])
                    ->action('Notification Action', route('mds.auth.first.reset', $$this->user_detail['token']))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
