<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationReceived extends Notification
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
        return (new MailMessage)
            ->subject('New Application Received')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new application for the position of "' . $this->application->job->title . '".')
            ->line('Candidate: ' . $this->application->candidate->name)
            ->action('View Application', url('/employer/applications/' . $this->application->id))
            ->line('Thank you for using our job board!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job->id,
            'job_title' => $this->application->job->title,
            'candidate_id' => $this->application->candidate->id,
            'candidate_name' => $this->application->candidate->name,
            'type' => 'new_application',
            'message' => $this->application->candidate->name . ' has applied for your job posting "' . $this->application->job->title . '".',
        ];
    }
}
