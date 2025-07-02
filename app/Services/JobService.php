<?php

namespace App\Services;

use App\Events\JobApproved;
use App\Models\Job;
use App\Models\User;
use App\Notifications\NewJobCreated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class JobService
{
    /**
     * Get paginated jobs with optional filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedJobs(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Job::with('employer');

        // Apply filters using scopes
        if (isset($filters['approved'])) {
            $query = $filters['approved'] ? $query->approved() : $query->pending();
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['location'])) {
            $query->byLocation($filters['location']);
        }

        if (!empty($filters['experience_level'])) {
            $query->byExperienceLevel($filters['experience_level']);
        }

        if (!empty($filters['salary_min']) || !empty($filters['salary_max'])) {
            $query->withinSalaryRange($filters['salary_min'] ?? null, $filters['salary_max'] ?? null);
        }

        if (!empty($filters['posted_days'])) {
            $query->postedWithinDays($filters['posted_days']);
        }

        // Sorting
        $sortBy = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';

        if ($sortBy === 'salary_max') {
            $query->orderByRaw('COALESCE(salary_max, 0) DESC');
        } elseif ($sortBy === 'salary_min') {
            $query->orderByRaw('COALESCE(salary_min, 0) ASC');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get job statistics
     *
     * @return array
     */
    public function getJobStatistics(): array
    {
        return [
            'total_jobs' => Job::count(),
            'approved_jobs' => Job::where('is_approved', true)->count(),
            'pending_jobs' => Job::where('is_approved', false)->count(),
            'recent_jobs' => Job::where('created_at', '>=', now()->subDays(7))->count(),
            'jobs_by_category' => Job::select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->pluck('total', 'category')
                ->toArray(),
        ];
    }

    /**
     * Create a new job
     *
     * @param array $data
     * @param User $employer
     * @return Job
     */
    public function createJob(array $data, User $employer): Job
    {
        return DB::transaction(function () use ($data, $employer) {
            // Handle company logo upload
            $companyLogo = null;
            if (!empty($data['company_logo'])) {
                $companyLogo = $data['company_logo']->store('company_logos', 'public');
            }

            $job = Job::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'location' => $data['location'],
                'category' => $data['category'],
                'type' => $data['type'],
                'experience_level' => $data['experience_level'] ?? null,
                'salary_min' => $data['salary_min'],
                'salary_max' => $data['salary_max'],
                'deadline' => $data['deadline'],
                'is_approved' => false,
                'employer_id' => $employer->id,
                'company_logo' => $companyLogo,
            ]);

            // Load employer relationship for notifications
            $job->load('employer');

            // Notify admins about new job posting
            $this->notifyAdminsAboutNewJob($job);

            return $job;
        });
    }

    /**
     * Update an existing job
     *
     * @param Job $job
     * @param array $data
     * @return Job
     */
    public function updateJob(Job $job, array $data): Job
    {
        return DB::transaction(function () use ($job, $data) {
            // Handle company logo upload
            if (!empty($data['company_logo'])) {
                // Delete old logo if exists
                if ($job->company_logo) {
                    Storage::disk('public')->delete($job->company_logo);
                }
                $job->company_logo = $data['company_logo']->store('company_logos', 'public');
            }

            $job->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'location' => $data['location'],
                'category' => $data['category'],
                'type' => $data['type'],
                'experience_level' => $data['experience_level'],
                'salary_min' => $data['salary_min'],
                'salary_max' => $data['salary_max'],
                'deadline' => $data['deadline'],
                'is_approved' => false, // Reset approval status
            ]);

            return $job->fresh();
        });
    }

    /**
     * Approve a job
     *
     * @param Job $job
     * @return Job
     */
    public function approveJob(Job $job): Job
    {
        return DB::transaction(function () use ($job) {
            $job->update(['is_approved' => true]);

            // Fire event to notify interested candidates
            event(new JobApproved($job));

            return $job->fresh();
        });
    }

    /**
     * Delete a job
     *
     * @param Job $job
     * @return bool
     */
    public function deleteJob(Job $job): bool
    {
        return DB::transaction(function () use ($job) {
            // Delete company logo if exists
            if ($job->company_logo) {
                Storage::disk('public')->delete($job->company_logo);
            }

            // Delete related applications
            $job->applications()->delete();

            return $job->delete();
        });
    }

    /**
     * Get pending jobs
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPendingJobs(int $perPage = 15): LengthAwarePaginator
    {
        return Job::pending()
            ->with('employer')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get recent jobs
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentJobs(int $limit = 5): Collection
    {
        return Job::with('employer')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get similar jobs
     *
     * @param Job $job
     * @param int $limit
     * @return Collection
     */
    public function getSimilarJobs(Job $job, int $limit = 3): Collection
    {
        return Job::approved()
            ->byCategory($job->category)
            ->where('id', '!=', $job->id)
            ->take($limit)
            ->get();
    }

    /**
     * Notify admins about new job posting
     *
     * @param Job $job
     * @return void
     */
    private function notifyAdminsAboutNewJob(Job $job): void
    {
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewJobCreated($job));
    }

    /**
     * Find job by ID
     *
     * @param int $id
     * @return Job
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findJobById(int $id): Job
    {
        return Job::with('employer')->findOrFail($id);
    }


}
