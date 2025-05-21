@extends('layouts.dashboard')

@section('title', 'Job Statistics')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Job Statistics</h1>
        <div>
            <a href="{{ route('admin.reports.applications') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-2"></i> Application Statistics
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
                <div class="card-title">Total Jobs</div>
                <div class="card-value">{{ array_sum($jobsByCategory->pluck('count')->toArray()) }}</div>
                <div class="text-muted small mt-2">
                    All time
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <div class="card-title">Locations</div>
                <div class="card-value">{{ $jobsByLocation->count() }}</div>
                <div class="text-muted small mt-2">
                    Unique locations
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                <div class="card-title">Categories</div>
                <div class="card-value">{{ $jobsByCategory->count() }}</div>
                <div class="text-muted small mt-2">
                    Unique categories
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="card-title">Job Types</div>
                <div class="card-value">{{ $jobsByType->count() }}</div>
                <div class="text-muted small mt-2">
                    Different job types
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Jobs by Month Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Jobs Posted by Month</h5>
                </div>
                <div class="card-body">
                    <canvas id="jobsByMonthChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Jobs by Category Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Jobs by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="jobsByCategoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Jobs by Location -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Top Locations</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Jobs</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalJobs = array_sum($jobsByLocation->pluck('count')->toArray());
                                @endphp
                                @foreach($jobsByLocation as $location)
                                    <tr>
                                        <td>{{ $location->location }}</td>
                                        <td>{{ $location->count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ ($location->count / $totalJobs) * 100 }}%;" aria-valuenow="{{ ($location->count / $totalJobs) * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div>{{ number_format(($location->count / $totalJobs) * 100, 1) }}%</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jobs by Type -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Jobs by Type</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>Job Type</th>
                                    <th>Jobs</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalJobsByType = array_sum($jobsByType->pluck('count')->toArray());
                                @endphp
                                @foreach($jobsByType as $type)
                                    <tr>
                                        <td>{{ ucfirst($type->type ?? 'N/A') }}</td>
                                        <td>{{ $type->count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ ($type->count / $totalJobsByType) * 100 }}%;" aria-valuenow="{{ ($type->count / $totalJobsByType) * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div>{{ number_format(($type->count / $totalJobsByType) * 100, 1) }}%</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Jobs by Month Chart
        const jobsByMonthCtx = document.getElementById('jobsByMonthChart').getContext('2d');
        new Chart(jobsByMonthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthLabels) !!},
                datasets: [{
                    label: 'Jobs Posted',
                    data: {!! json_encode($jobCounts) !!},
                    fill: false,
                    borderColor: '#0d6efd',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Jobs by Category Chart
        const jobsByCategoryCtx = document.getElementById('jobsByCategoryChart').getContext('2d');
        new Chart(jobsByCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($jobsByCategory->pluck('category')->toArray()) !!},
                datasets: [{
                    data: {!! json_encode($jobsByCategory->pluck('count')->toArray()) !!},
                    backgroundColor: [
                        '#0d6efd',
                        '#6610f2',
                        '#6f42c1',
                        '#d63384',
                        '#fd7e14',
                        '#20c997',
                        '#0dcaf0',
                        '#198754',
                        '#ffc107',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endpush
