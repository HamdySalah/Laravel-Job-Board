@extends('layouts.main')

@section('title', 'Pending Jobs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
<div class="container dashboard-container">
    <div class="dashboard-header">
        <h1>Pending Jobs</h1>
        <p class="text-muted">Review and approve job listings submitted by employers</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-tasks me-2"></i>Jobs Awaiting Approval</h5>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
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
                            <table class="table table-hover admin-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Employer</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th>Posted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobs as $job)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('job-listings.show', $job->id) }}" class="text-decoration-none fw-bold">
                                                        {{ $job->title }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $job->employer->name }}</td>
                                            <td>{{ $job->location }}</td>
                                            <td><span class="badge bg-info">{{ ucfirst($job->type) }}</span></td>
                                            <td>{{ $job->created_at->diffForHumans() }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('job-listings.show', $job->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Approve Job">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.jobs.reject', $job->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject Job" onclick="return confirm('Are you sure you want to reject this job?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $jobs->links() }}
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fa-2x"></i>
                            <div>
                                There are no pending jobs that require approval at this time.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection