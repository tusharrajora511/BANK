<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserCredentials extends Notification
{
    use Queueable; // Use the Queueable trait to allow the notification to be queued

    protected $password; // Store the password to be sent in the notification


    // Constructor to initialize the password property
    // This constructor is called when the notification is created
    public function __construct($password)
    {
        $this->password = $password;
    }

    // The via method determines which channels the notification should be sent through
    // In this case, we are using the mail channel to send the notification via email
    public function via($notifiable)
    {
        return ['mail'];
    }
    // The toMail method builds the email message that will be sent to the user
    // It uses the MailMessage class to create the email content

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Rajora Banks Account Credentials')
            ->greeting('Welcome to Rajora Bank!')
            ->line('Your account has been created successfully.')
            ->line('Here are your login credentials:')
            ->line('Email: ' . $notifiable->email)
            ->line('Password: ' . $this->password)
            ->line('Please login and change your password for security reasons.')
            ->action('Login Now', route('login'))
            ->line('Thank you for using our application!')
            ->line('Thanks')
            ->line('Rajora Bank Team');
    }
} 