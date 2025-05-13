@extends('layouts.main')

@section('title', 'Employer Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/employer-dashboard.css') }}">
@endpush

@section('content')
<div class="container dashboard-container">
    <div class="dashboard-header">
        <h1>Employer Dashboard</h1>
        <p class="text-muted">Manage your job listings and applications</p>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <!-- Employer Profile Card -->
            <div class="dashboard-card mb-4">
                <div class="profile-summary">
                    @if(auth()->user()->company_logo)
                        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="{{ auth()->user()->company_name }}" class="profile-logo">
                    @else
                        <div class="profile-avatar">
                            {{ substr(auth()->user()->company_name ?? auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <h5 class="profile-name">{{ auth()->user()->company_name ?? auth()->user()->name }}</h5>
                    <p class="profile-email">{{ auth()->user()->email }}</p>
                    <a href="{{ route('employer.profile') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="dashboard-card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Job Stats</h5>
                </div>
                <div class="card-body p-0">
                    <div class="stats-item">
                        <span class="stats-label">Total Jobs</span>
                        <span class="badge bg-primary rounded-pill">{{ $stats['totalJobs'] }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Active Jobs</span>
                        <span class="badge bg-success rounded-pill">{{ $stats['approvedJobs'] }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Pending Approval</span>
                        <span class="badge bg-warning rounded-pill">{{ $stats['pendingJobs'] }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Total Applications</span>
                        <span class="badge bg-info rounded-pill">{{ $stats['totalApplications'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Welcome Message -->
            <div class="welcome-card">
                <h2><i class="fas fa-building me-2"></i>Welcome, {{ auth()->user()->company_name ?? auth()->user()->name }}!</h2>
                <p>
                    Manage your job listings and track applications from candidates.
                    Find the perfect talent for your company!
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ url('/add_post') }}" class="btn btn-light">
                        <i class="fas fa-plus-circle me-2"></i>Post a New Job
                    </a>
                    <a href="{{ route('employer.profile') }}" class="btn btn-outline-light">
                        <i class="fas fa-cog me-2"></i>Account Settings
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="quick-actions">
                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h6 class="quick-action-title">Post a Job</h6>
                            <p class="quick-action-description">Create a new job listing</p>
                            <a href="{{ url('/add_post') }}" class="btn btn-sm btn-primary mt-2">Create</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h6 class="quick-action-title">Applications</h6>
                            <p class="quick-action-description">Review candidate applications</p>
                            <a href="#job-listings" class="btn btn-sm btn-primary mt-2">View</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h6 class="quick-action-title">Analytics</h6>
                            <p class="quick-action-description">Track job performance</p>
                            <a href="#" class="btn btn-sm btn-primary mt-2">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="dashboard-card" id="job-listings">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-briefcase me-2"></i>My Job Listings</h5>
                    <a href="{{ url('/add_post') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($jobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover job-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Status</th>
                                        <th>Posted</th>
                                        <th>Applications</th>
                                        <th>Deadline</th>
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
                                                <span class="job-status {{ $job->is_approved ? 'status-approved' : 'status-pending' }}">
                                                    {{ $job->is_approved ? 'Approved' : 'Pending Approval' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="far fa-calendar-alt me-2 text-muted"></i>
                                                    {{ $job->created_at->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('employer.job-applications.view', $job->id) }}" class="text-decoration-none d-flex align-items-center">
                                                    <i class="fas fa-users me-2 text-primary"></i>
                                                    <span>{{ $job->applications_count }}</span>
                                                    <span class="application-count">{{ $job->applications_count }}</span>
                                                </a>
                                            </td>
                                            <td>
                                                @if(\Carbon\Carbon::now()->gt($job->deadline))
                                                    <span class="text-danger">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Expired
                                                    </span>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-hourglass-half me-2 text-muted"></i>
                                                        {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown action-buttons">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $job->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $job->id }}">
                                                        <li>
                                                            <a href="{{ route('job-listings.show', $job->id) }}" class="dropdown-item">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('job-listings.edit', $job->id) }}" class="dropdown-item">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('employer.job-applications.view', $job->id) }}" class="dropdown-item">
                                                                <i class="fas fa-users"></i> Applications
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('job-listings.destroy', $job->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this job?')">
                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fa-2x"></i>
                            <div>
                                You haven't posted any jobs yet.
                                <a href="{{ url('/add_post') }}" class="alert-link">Post your first job</a> to start receiving applications.
                            </div>
                        </div>

                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-jobs.svg') }}" alt="No Jobs" class="img-fluid mb-4" style="max-width: 250px; opacity: 0.7;">
                            <h4>No Job Listings Yet</h4>
                            <p class="text-muted">Create your first job listing to start receiving applications from qualified candidates.</p>
                            <a href="{{ url('/add_post') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Post a Job
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
