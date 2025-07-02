<?php

namespace App\Listeners;

use App\Events\JobApproved;
use App\Models\User;
use App\Notifications\NewJobPosted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyInterestedCandidates implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JobApproved $event): void
    {
        $job = $event->job;

        // Find candidates who might be interested in this job category
        $interestedCandidates = User::where('role', 'candidate')
            ->where(function($query) use ($job) {
                $query->where('skills', 'like', "%{$job->category}%")
                      ->orWhere('bio', 'like', "%{$job->category}%");
            })
            ->get();

        // Send notifications to interested candidates
        Notification::send($interestedCandidates, new NewJobPosted($job));
    }

    /**
     * Handle a job failure.
     */
    public function failed(JobApproved $event, \Throwable $exception): void
    {
        // Log the failure or handle it appropriately
        \Log::error('Failed to notify interested candidates about job approval', [
            'job_id' => $event->job->id,
            'error' => $exception->getMessage()
        ]);
    }
}
