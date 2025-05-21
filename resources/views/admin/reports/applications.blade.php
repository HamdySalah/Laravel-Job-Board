@extends('layouts.dashboard')

@section('title', 'Application Statistics')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Application Statistics</h1>
        <div>
            <a href="{{ route('admin.reports.jobs') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i> Job Statistics
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="card-title">Total Applications</div>
                <div class="card-value">{{ array_sum($applicationCounts) }}</div>
                <div class="text-muted small mt-2">
                    All time
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
                <div class="card-value">{{ $applicationsByCategory->count() }}</div>
                <div class="text-muted small mt-2">
                    Categories with applications
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <div class="card-title">Statuses</div>
                <div class="card-value">{{ $applicationsByStatus->count() }}</div>
                <div class="text-muted small mt-2">
                    Different application statuses
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="card-title">Monthly Average</div>
                <div class="card-value">{{ count($applicationCounts) > 0 ? round(array_sum($applicationCounts) / count($applicationCounts)) : 0 }}</div>
                <div class="text-muted small mt-2">
                    Applications per month
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Applications by Month Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Applications by Month</h5>
                </div>
                <div class="card-body">
                    <canvas id="applicationsByMonthChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Applications by Status Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Applications by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="applicationsByStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Applications by Category -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Applications by Job Category</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Applications</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalApplicationsByCategory = array_sum($applicationsByCategory->pluck('count')->toArray());
                                @endphp
                                @foreach($applicationsByCategory as $category)
                                    <tr>
                                        <td>{{ $category->category ?? 'N/A' }}</td>
                                        <td>{{ $category->count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ ($category->count / $totalApplicationsByCategory) * 100 }}%;" aria-valuenow="{{ ($category->count / $totalApplicationsByCategory) * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div>{{ number_format(($category->count / $totalApplicationsByCategory) * 100, 1) }}%</div>
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

    <!-- Application Conversion Metrics -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Application Conversion Metrics</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Application Rate</h6>
                        <div class="display-6 fw-bold text-primary">{{ isset($conversionMetrics['applicationRate']) ? number_format($conversionMetrics['applicationRate'], 1) : '0.0' }}%</div>
                        <div class="small text-muted">Applications per job view</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Interview Rate</h6>
                        <div class="display-6 fw-bold text-primary">{{ isset($conversionMetrics['interviewRate']) ? number_format($conversionMetrics['interviewRate'], 1) : '0.0' }}%</div>
                        <div class="small text-muted">Interviews per application</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Offer Rate</h6>
                        <div class="display-6 fw-bold text-primary">{{ isset($conversionMetrics['offerRate']) ? number_format($conversionMetrics['offerRate'], 1) : '0.0' }}%</div>
                        <div class="small text-muted">Offers per interview</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Acceptance Rate</h6>
                        <div class="display-6 fw-bold text-primary">{{ isset($conversionMetrics['acceptanceRate']) ? number_format($conversionMetrics['acceptanceRate'], 1) : '0.0' }}%</div>
                        <div class="small text-muted">Acceptances per offer</div>
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
        // Applications by Month Chart
        const applicationsByMonthCtx = document.getElementById('applicationsByMonthChart').getContext('2d');
        new Chart(applicationsByMonthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthLabels) !!},
                datasets: [{
                    label: 'Applications',
                    data: {!! json_encode($applicationCounts) !!},
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
        
        // Applications by Status Chart
        const applicationsByStatusCtx = document.getElementById('applicationsByStatusChart').getContext('2d');
        new Chart(applicationsByStatusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($applicationsByStatus->pluck('status')->toArray()) !!},
                datasets: [{
                    data: {!! json_encode($applicationsByStatus->pluck('count')->toArray()) !!},
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
