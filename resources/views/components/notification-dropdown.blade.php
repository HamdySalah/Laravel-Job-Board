<div class="dropdown notification-dropdown">
    <button class="btn btn-link position-relative p-0 mx-2" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell fa-lg"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                <span class="visually-hidden">unread notifications</span>
            </span>
        @endif
    </button>
    
    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 notification-dropdown-menu p-0" aria-labelledby="notificationDropdown">
        <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <h6 class="mb-0 fw-bold">Notifications</h6>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-decoration-none p-0">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
        
        <div class="notification-body" style="max-height: 300px; overflow-y: auto;">
            @if(auth()->user()->notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach(auth()->user()->notifications->take(5) as $notification)
                        <div class="list-group-item notification-item {{ $notification->read_at ? '' : 'unread' }} p-2">
                            <div class="d-flex align-items-start">
                                @php
                                    $iconClass = 'fas fa-bell';
                                    $bgClass = 'new-job';
                                    
                                    if (isset($notification->data['type'])) {
                                        switch($notification->data['type']) {
                                            case 'new_job':
                                                $iconClass = 'fas fa-briefcase';
                                                $bgClass = 'new-job';
                                                break;
                                            case 'application_status':
                                                $iconClass = 'fas fa-clipboard-check';
                                                $bgClass = 'application-status';
                                                break;
                                            case 'new_application':
                                                $iconClass = 'fas fa-file-alt';
                                                $bgClass = 'new-application';
                                                break;
                                            case 'new_user':
                                                $iconClass = 'fas fa-user-plus';
                                                $bgClass = 'new-user';
                                                break;
                                            case 'new_job_admin':
                                                $iconClass = 'fas fa-exclamation-circle';
                                                $bgClass = 'new-job-admin';
                                                break;
                                        }
                                    }
                                @endphp
                                
                                <div class="notification-icon {{ $bgClass }} me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    <i class="{{ $iconClass }}"></i>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <p class="mb-0 small fw-medium">{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        
                                        @if(!$notification->read_at)
                                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm p-0 text-primary" style="font-size: 0.7rem;">
                                                    Mark as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                    <p class="mb-0 text-muted">No notifications</p>
                </div>
            @endif
        </div>
        
        <div class="notification-footer p-2 border-top text-center">
            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link text-decoration-none w-100">
                View all notifications
            </a>
        </div>
    </div>
</div>

<style>
    .notification-dropdown .btn-link {
        color: #333;
    }
    
    .notification-dropdown .btn-link:hover {
        color: #0d6efd;
    }
    
    .notification-dropdown-menu {
        width: 320px;
    }
    
    .notification-badge {
        font-size: 0.6rem;
        padding: 0.25rem 0.4rem;
    }
    
    .notification-item {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    
    .notification-item:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .notification-item.unread {
        border-left-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .notification-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
    }
    
    .notification-icon.new-job {
        background-color: #0d6efd;
    }
    
    .notification-icon.application-status {
        background-color: #6f42c1;
    }
    
    .notification-icon.new-application {
        background-color: #20c997;
    }
    
    .notification-icon.new-user {
        background-color: #fd7e14;
    }
    
    .notification-icon.new-job-admin {
        background-color: #dc3545;
    }
</style>
