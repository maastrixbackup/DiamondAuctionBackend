<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SellerPasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $email;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = env('SITE_URL') . "seller-reset-password?token={$this->token}&email={$this->email}";

        return (new MailMessage)
            ->subject('Reset Your Seller Account Password')
            ->greeting('Hello!')
            ->line('We received a request to reset your password for your seller account.')
            ->action('Reset Password', $url)
            ->line('This reset link will expire soon. If you did not request this, please ignore this email.');
    }
}
