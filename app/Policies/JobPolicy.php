<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->role === 'employer';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Job $job)
    {
        // Anyone can view approved jobs
        if ($job->is_approved) {
            return true;
        }

        // Employers can view their own jobs even if not approved
        if ($user->role === 'employer' && $job->employer_id === $user->id) {
            return true;
        }

        // Admins can view any job
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role === 'employer';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Job $job)
    {
        // Only the employer who created the job can update it
        return $user->role === 'employer' && $job->employer_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Job $job)
    {
        // Only the employer who created the job can delete it
        return $user->role === 'employer' && $job->employer_id === $user->id;
    }

    /**
     * Determine whether the user can manage applications for the job.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function manageApplications(User $user, Job $job)
    {
        // Only the employer who created the job can manage its applications
        return $user->role === 'employer' && $job->employer_id === $user->id;
    }
}
