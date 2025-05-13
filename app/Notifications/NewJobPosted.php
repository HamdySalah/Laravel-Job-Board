<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobPosted extends Notification
{
    use Queueable;

    protected $job;

    /**
     * Create a new notification instance.
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
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
            ->subject('New Job Posted in Your Preferred Category')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new job has been posted in your preferred category: ' . $this->job->category)
            ->line('Job Title: ' . $this->job->title)
            ->line('Company: ' . $this->job->employer->name)
            ->line('Location: ' . $this->job->location)
            ->action('View Job', url('/job-listings/' . $this->job->id))
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
            'job_id' => $this->job->id,
            'title' => $this->job->title,
            'employer' => $this->job->employer->name,
            'category' => $this->job->category,
            'type' => 'new_job',
            'message' => 'A new job has been posted in your preferred category: ' . $this->job->category,
        ];
    }
}
