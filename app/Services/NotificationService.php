<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Get paginated notifications for a user
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedNotifications(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $user->notifications()->paginate($perPage);
    }

    /**
     * Get unread notifications for a user
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getUnreadNotifications(User $user, int $limit = 5): Collection
    {
        return $user->unreadNotifications()->take($limit)->get();
    }

    /**
     * Get notification count for a user
     *
     * @param User $user
     * @return array
     */
    public function getNotificationCounts(User $user): array
    {
        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->readNotifications()->count(),
        ];
    }

    /**
     * Mark a notification as read
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        
        return true;
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param User $user
     * @return int
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Delete a notification
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function deleteNotification(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->findOrFail($notificationId);
        return $notification->delete();
    }

    /**
     * Delete all notifications for a user
     *
     * @param User $user
     * @return int
     */
    public function deleteAllNotifications(User $user): int
    {
        return $user->notifications()->delete();
    }

    /**
     * Get notification statistics for admin dashboard
     *
     * @return array
     */
    public function getNotificationStatistics(): array
    {
        return [
            'total_notifications' => DatabaseNotification::count(),
            'unread_notifications' => DatabaseNotification::whereNull('read_at')->count(),
            'notifications_today' => DatabaseNotification::whereDate('created_at', today())->count(),
            'notifications_this_week' => DatabaseNotification::where('created_at', '>=', now()->subWeek())->count(),
            'notifications_by_type' => DatabaseNotification::select(
                DB::raw("JSON_EXTRACT(data, '$.type') as type"),
                DB::raw('count(*) as count')
            )
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }

    /**
     * Clean up old read notifications
     *
     * @param int $daysOld
     * @return int
     */
    public function cleanupOldNotifications(int $daysOld = 30): int
    {
        return DatabaseNotification::whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Get recent notifications for dashboard
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getRecentNotifications(User $user, int $limit = 5): Collection
    {
        return $user->notifications()
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Format notifications for API response
     *
     * @param Collection $notifications
     * @return array
     */
    public function formatNotificationsForApi(Collection $notifications): array
    {
        return $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'general',
                'message' => $notification->data['message'] ?? 'You have a new notification.',
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
                'is_read' => !is_null($notification->read_at),
            ];
        })->toArray();
    }
}
