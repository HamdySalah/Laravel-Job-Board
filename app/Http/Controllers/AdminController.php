<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\JobResource;
use App\Http\Resources\UserResource;
use App\Models\Application;
use App\Services\JobService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly JobService $jobService
    ) {}

    /**
     * Display the admin dashboard with summary statistics
     */
    public function dashboard(): View
    {
        $jobs = JobResource::collection($this->jobService->getRecentJobs());
        $users = UserResource::collection($this->userService->getRecentUsers());

        $stats = [
            ...$this->userService->getUserStatistics(),
            ...$this->jobService->getJobStatistics(),
            'totalApplications' => Application::count(),
        ];

        return view('admin.dashboard', compact('jobs', 'users', 'stats'));
    }

    /**
     * Display a list of all users for management
     */
    public function manageUsers(Request $request): View
    {
        $filters = $request->only(['role', 'search', 'created_from', 'created_to', 'verified']);
        $users = $this->userService->getPaginatedUsers($filters, 15);

        return view('admin.users.manage', compact('users'));
    }

    /**
     * Show the form for editing a user
     */
    public function editUser(int $id): View
    {
        $user = $this->userService->findUserById($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user's information
     */
    public function updateUser(UpdateUserRequest $request, int $id): RedirectResponse
    {
        try {
            $user = $this->userService->findUserById($id);
            $this->userService->updateUser($user, $request->validated());

            return redirect()
                ->route('admin.users.manage')
                ->with('success', "User '{$user->name}' has been updated successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser(int $id): RedirectResponse
    {
        try {
            $user = $this->userService->findUserById($id);
            $userName = $user->name;

            $this->userService->deleteUser($user, auth()->user());

            return redirect()
                ->route('admin.users.manage')
                ->with('success', "User '{$userName}' has been deleted successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display a list of pending jobs awaiting approval
     */
    public function pendingJobs(): View
    {
        $jobs = $this->jobService->getPendingJobs(15);
        return view('admin.jobs.pending', compact('jobs'));
    }

    /**
     * Approve a job listing
     */
    public function approveJob(int $id): RedirectResponse
    {
        try {
            $job = $this->jobService->findJobById($id);
            $this->jobService->approveJob($job);

            return redirect()
                ->route('admin.jobs.pending')
                ->with('success', "Job '{$job->title}' has been approved and is now visible to candidates.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to approve job: ' . $e->getMessage());
        }
    }

    /**
     * Reject a job listing
     */
    public function rejectJob(int $id): RedirectResponse
    {
        try {
            $job = $this->jobService->findJobById($id);
            $jobTitle = $job->title;

            $this->jobService->deleteJob($job);

            return redirect()
                ->route('admin.jobs.pending')
                ->with('success', "Job '{$jobTitle}' has been rejected and removed from the system.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to reject job: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard data as JSON (for API endpoints)
     */
    public function dashboardData(): JsonResponse
    {
        $data = [
            'stats' => [
                ...$this->userService->getUserStatistics(),
                ...$this->jobService->getJobStatistics(),
                'totalApplications' => Application::count(),
            ],
            'recent_jobs' => JobResource::collection($this->jobService->getRecentJobs()),
            'recent_users' => UserResource::collection($this->userService->getRecentUsers()),
        ];

        return response()->json($data);
    }
}