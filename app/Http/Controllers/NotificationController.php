<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Display a listing of the user's notifications.
     */
    public function index(): View
    {
        $notifications = $this->notificationService->getPaginatedNotifications(Auth::user(), 10);
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        try {
            $this->notificationService->markAsRead(Auth::user(), $id);
            return redirect()->back()->with('success', 'Notification marked as read.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to mark notification as read.');
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        try {
            $this->notificationService->markAllAsRead(Auth::user());
            return redirect()->back()->with('success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to mark all notifications as read.');
        }
    }

    /**
     * Delete a notification.
     */
    public function delete(string $id): RedirectResponse
    {
        try {
            $this->notificationService->deleteNotification(Auth::user(), $id);
            return redirect()->back()->with('success', 'Notification deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete notification.');
        }
    }

    /**
     * Delete all notifications.
     */
    public function deleteAll(): RedirectResponse
    {
        try {
            $this->notificationService->deleteAllNotifications(Auth::user());
            return redirect()->back()->with('success', 'All notifications deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete all notifications.');
        }
    }

    /**
     * Get unread notifications for AJAX requests.
     */
    public function getUnreadNotifications(): JsonResponse
    {
        $user = Auth::user();
        $unreadNotifications = $this->notificationService->getUnreadNotifications($user, 5);

        return response()->json([
            'count' => $this->notificationService->getNotificationCounts($user)['unread'],
            'notifications' => $this->notificationService->formatNotificationsForApi($unreadNotifications)
        ]);
    }
}
