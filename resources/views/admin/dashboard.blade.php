@extends('layouts.main')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
<div class="container dashboard-container">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p class="text-muted">Manage jobs, users, and system settings</p>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <!-- Admin Profile Card -->
            <div class="dashboard-card mb-4">
                <div class="profile-summary">
                    <div class="profile-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <h5 class="profile-name">{{ auth()->user()->name }}</h5>
                    <p class="profile-email">{{ auth()->user()->email }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="dashboard-card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>System Stats</h5>
                </div>
                <div class="card-body p-0">
                    <div class="stats-item">
                        <span class="stats-label">Total Jobs</span>
                        <span class="badge bg-primary rounded-pill">{{ $stats['totalJobs'] }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Pending Jobs</span>
                        <span class="badge bg-warning rounded-pill">{{ $stats['pendingJobs'] }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Total Users</span>
                        <span class="badge bg-info rounded-pill">{{ $stats['totalUsers'] }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Applications</span>
                        <span class="badge bg-success rounded-pill">{{ $stats['totalApplications'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Welcome Message -->
            <div class="welcome-card">
                <h2><i class="fas fa-crown me-2"></i>Welcome, Administrator!</h2>
                <p>
                    Manage the job board platform, approve job listings, and oversee user accounts.
                    Keep the platform running smoothly and ensure quality content.
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('admin.jobs.pending') }}" class="btn btn-light">
                        <i class="fas fa-tasks me-2"></i>Pending Jobs
                    </a>
                    <a href="{{ route('admin.users.manage') }}" class="btn btn-outline-light">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="quick-actions">
                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="quick-action-title">Approve Jobs</h6>
                            <p class="quick-action-description">Review and approve pending job listings</p>
                            <a href="{{ route('admin.jobs.pending') }}" class="btn btn-sm btn-primary mt-2">Manage</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h6 class="quick-action-title">User Management</h6>
                            <p class="quick-action-description">Manage user accounts and roles</p>
                            <a href="{{ route('admin.users.manage') }}" class="btn btn-sm btn-primary mt-2">Manage</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h6 class="quick-action-title">Analytics</h6>
                            <p class="quick-action-description">View platform statistics and reports</p>
                            <a href="#" class="btn btn-sm btn-primary mt-2">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Jobs -->
            <div class="dashboard-card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-briefcase me-2"></i>Latest Job Listings</h5>
                    <a href="{{ route('admin.jobs.pending') }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($jobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover admin-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Employer</th>
                                        <th>Status</th>
                                        <th>Posted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $job)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('job-listings.show', $job->id) }}" class="text-decoration-none fw-bold">
                                                        {{ $job->title }}
                                                    </a>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }} |
                                                        <i class="fas fa-briefcase me-1"></i>{{ ucfirst($job->type) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $job->employer->name ?? 'Unknown' }}
                                            </td>
                                            <td>
                                                <span class="status-badge {{ $job->is_approved ? 'status-approved' : 'status-pending' }}">
                                                    {{ $job->is_approved ? 'Approved' : 'Pending' }}
                                                </span>
                                            </td>
                                            <td>{{ $job->created_at->diffForHumans() }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('job-listings.show', $job->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(!$job->is_approved)
                                                        <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.jobs.reject', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to reject this job?')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No job listings found.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Latest Users -->
            <div class="dashboard-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Latest Users</h5>
                    <a href="{{ route('admin.users.manage') }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover admin-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="status-badge status-{{ $user->role }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No users found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
