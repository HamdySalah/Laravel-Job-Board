@extends('layouts.main')

@section('title', 'My Notifications')

@push('styles')
<style>
    .notification-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .notification-item:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .notification-item.unread {
        border-left-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .notification-item.unread:hover {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
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
    
    .notification-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .notification-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .notification-item:hover .notification-actions {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">My Notifications</h1>
                <div class="d-flex gap-2">
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-check-double me-1"></i> Mark All as Read
                        </button>
                    </form>
                    <form action="{{ route('notifications.delete-all') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete all notifications?')">
                            <i class="fas fa-trash me-1"></i> Delete All
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ $notification->read_at ? '' : 'unread' }} p-3">
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
                                        
                                        <div class="notification-icon {{ $bgClass }} me-3">
                                            <i class="{{ $iconClass }}"></i>
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1 fw-bold">
                                                    @if(isset($notification->data['type']))
                                                        @switch($notification->data['type'])
                                                            @case('new_job')
                                                                New Job in {{ $notification->data['category'] }}
                                                                @break
                                                            @case('application_status')
                                                                Application Status Update
                                                                @break
                                                            @case('new_application')
                                                                New Job Application
                                                                @break
                                                            @case('new_user')
                                                                New User Registration
                                                                @break
                                                            @case('new_job_admin')
                                                                New Job Requires Approval
                                                                @break
                                                            @default
                                                                Notification
                                                        @endswitch
                                                    @else
                                                        Notification
                                                    @endif
                                                </h6>
                                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            
                                            <p class="mb-1">{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <div>
                                                    @if(isset($notification->data['type']))
                                                        @switch($notification->data['type'])
                                                            @case('new_job')
                                                                <a href="{{ route('job-listings.show', $notification->data['job_id']) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye me-1"></i> View Job
                                                                </a>
                                                                @break
                                                            @case('application_status')
                                                                <a href="{{ route('candidate.applications.show', $notification->data['application_id']) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye me-1"></i> View Application
                                                                </a>
                                                                @break
                                                            @case('new_application')
                                                                <a href="{{ route('employer.applications.show', $notification->data['application_id']) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye me-1"></i> View Application
                                                                </a>
                                                                @break
                                                            @case('new_user')
                                                                <a href="{{ route('admin.users.manage') }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye me-1"></i> View User
                                                                </a>
                                                                @break
                                                            @case('new_job_admin')
                                                                <a href="{{ route('admin.jobs.pending') }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye me-1"></i> Review Job
                                                                </a>
                                                                @break
                                                        @endswitch
                                                    @endif
                                                </div>
                                                
                                                <div class="notification-actions">
                                                    @if(!$notification->read_at)
                                                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-check"></i> Mark as Read
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center p-3">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                            <h4>No Notifications</h4>
                            <p class="text-muted">You don't have any notifications at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
