<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobCreated extends Notification
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
            ->subject('New Job Posting Requires Approval')
            ->greeting('Hello Admin!')
            ->line('A new job has been posted and requires your approval.')
            ->line('Job Title: ' . $this->job->title)
            ->line('Employer: ' . $this->job->employer->name)
            ->line('Category: ' . $this->job->category)
            ->action('Review Job', url('/admin/jobs/pending'))
            ->line('Thank you for managing our job board!');
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
            'employer_id' => $this->job->employer->id,
            'employer_name' => $this->job->employer->name,
            'category' => $this->job->category,
            'type' => 'new_job_admin',
            'message' => 'New job posting "' . $this->job->title . '" by ' . $this->job->employer->name . ' requires approval.',
        ];
    }
}
