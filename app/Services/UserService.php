<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\NewUserRegistered;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class UserService
{
    /**
     * Get paginated users with optional filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        // Apply filters
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get user statistics
     *
     * @return array
     */
    public function getUserStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'admin_count' => User::where('role', 'admin')->count(),
            'employer_count' => User::where('role', 'employer')->count(),
            'candidate_count' => User::where('role', 'candidate')->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'email_verified_at' => $data['email_verified_at'] ?? null,
            ]);

            // Notify admins about new user registration
            $this->notifyAdminsAboutNewUser($user);

            return $user;
        });
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
            ]);

            return $user->fresh();
        });
    }

    /**
     * Delete a user
     *
     * @param User $user
     * @param User $currentUser
     * @return bool
     * @throws \Exception
     */
    public function deleteUser(User $user, User $currentUser): bool
    {
        if ($user->id === $currentUser->id) {
            throw new \Exception('You cannot delete your own account.');
        }

        return DB::transaction(function () use ($user) {
            // Delete related data if needed
            $user->notifications()->delete();
            
            return $user->delete();
        });
    }

    /**
     * Get users by role
     *
     * @param string $role
     * @return Collection
     */
    public function getUsersByRole(string $role): Collection
    {
        return User::where('role', $role)->get();
    }

    /**
     * Get recent users
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentUsers(int $limit = 5): Collection
    {
        return User::latest()->take($limit)->get();
    }

    /**
     * Find user by ID
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Notify admins about new user registration
     *
     * @param User $newUser
     * @return void
     */
    private function notifyAdminsAboutNewUser(User $newUser): void
    {
        $admins = $this->getUsersByRole('admin');
        Notification::send($admins, new NewUserRegistered($newUser));
    }
}
