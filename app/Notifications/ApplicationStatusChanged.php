<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification
{
    use Queueable;

    protected $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'reviewing' => 'Your application is now being reviewed.',
            'interviewed' => 'You have been selected for an interview.',
            'accepted' => 'Congratulations! Your application has been accepted.',
            'rejected' => 'We regret to inform you that your application has been rejected.'
        ];

        $message = $statusMessages[$this->application->status] ?? 'Your application status has been updated.';
        
        return (new MailMessage)
            ->subject('Your Job Application Status Has Changed')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your application for the position of "' . $this->application->job->title . '" has been updated.')
            ->line($message)
            ->action('View Application', url('/candidate/applications/' . $this->application->id))
            ->line('Thank you for using our job board!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'reviewing' => 'under review',
            'interviewed' => 'selected for interview',
            'accepted' => 'accepted',
            'rejected' => 'rejected'
        ];
        
        $statusLabel = $statusLabels[$this->application->status] ?? 'updated';
        
        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job->id,
            'job_title' => $this->application->job->title,
            'employer' => $this->application->job->employer->name,
            'status' => $this->application->status,
            'type' => 'application_status',
            'message' => 'Your application for "' . $this->application->job->title . '" has been ' . $statusLabel . '.',
        ];
    }
}
