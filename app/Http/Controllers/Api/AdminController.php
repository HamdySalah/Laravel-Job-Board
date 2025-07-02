<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Http\Resources\UserResource;
use App\Services\JobService;
use App\Services\NotificationService;
use App\Services\UserService;
use App\Traits\HasApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use HasApiResponses;

    public function __construct(
        private readonly UserService $userService,
        private readonly JobService $jobService,
        private readonly NotificationService $notificationService
    ) {
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboardStats(): JsonResponse
    {
        $stats = [
            'users' => $this->userService->getUserStatistics(),
            'jobs' => $this->jobService->getJobStatistics(),
            'notifications' => $this->notificationService->getNotificationStatistics(),
        ];

        return $this->successResponse($stats, 'Dashboard statistics retrieved successfully');
    }

    /**
     * Get recent activities
     */
    public function recentActivities(): JsonResponse
    {
        $data = [
            'recent_jobs' => JobResource::collection($this->jobService->getRecentJobs(10)),
            'recent_users' => UserResource::collection($this->userService->getRecentUsers(10)),
        ];

        return $this->successResponse($data, 'Recent activities retrieved successfully');
    }

    /**
     * Get pending jobs for approval
     */
    public function pendingJobs(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $jobs = $this->jobService->getPendingJobs($perPage);

        return $this->successResponse(
            JobResource::collection($jobs)->response()->getData(true),
            'Pending jobs retrieved successfully'
        );
    }

    /**
     * Approve a job
     */
    public function approveJob(int $jobId): JsonResponse
    {
        try {
            $job = $this->jobService->findJobById($jobId);
            $approvedJob = $this->jobService->approveJob($job);

            return $this->successResponse(
                new JobResource($approvedJob),
                'Job approved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to approve job: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reject a job
     */
    public function rejectJob(int $jobId): JsonResponse
    {
        try {
            $job = $this->jobService->findJobById($jobId);
            $this->jobService->deleteJob($job);

            return $this->successResponse(null, 'Job rejected and removed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reject job: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get users with filters
     */
    public function users(Request $request): JsonResponse
    {
        $filters = $request->only(['role', 'search', 'created_from', 'created_to', 'verified']);
        $perPage = $request->get('per_page', 15);
        
        $users = $this->userService->getPaginatedUsers($filters, $perPage);

        return $this->successResponse(
            UserResource::collection($users)->response()->getData(true),
            'Users retrieved successfully'
        );
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'role' => 'required|in:admin,employer,candidate',
        ]);

        try {
            $user = $this->userService->findUserById($userId);
            $updatedUser = $this->userService->updateUser($user, $request->validated());

            return $this->successResponse(
                new UserResource($updatedUser),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(int $userId): JsonResponse
    {
        try {
            $user = $this->userService->findUserById($userId);
            $this->userService->deleteUser($user, auth()->user());

            return $this->successResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
