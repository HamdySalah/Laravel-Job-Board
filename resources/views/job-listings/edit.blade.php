@extends('layouts.main')

@section('title', 'Edit Job Listing')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Job Listing</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('job-listings.update', $job->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Basic Information</h5>
                        </div>

                        <!-- Job Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $job->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Job Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $job->category) }}" required>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Job Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Job Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Job Type</option>
                                <option value="full-time" {{ old('type', $job->type) == 'full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="part-time" {{ old('type', $job->type) == 'part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="remote" {{ old('type', $job->type) == 'remote' ? 'selected' : '' }}>Remote</option>
                                <option value="contract" {{ old('type', $job->type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="internship" {{ old('type', $job->type) == 'internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Experience Level -->
                        <div class="mb-3">
                            <label for="experience_level" class="form-label">Experience Level</label>
                            <select class="form-select @error('experience_level') is-invalid @enderror" id="experience_level" name="experience_level">
                                <option value="">Select Experience Level</option>
                                <option value="entry" {{ old('experience_level', $job->experience_level) == 'entry' ? 'selected' : '' }}>Entry Level</option>
                                <option value="mid" {{ old('experience_level', $job->experience_level) == 'mid' ? 'selected' : '' }}>Mid Level</option>
                                <option value="senior" {{ old('experience_level', $job->experience_level) == 'senior' ? 'selected' : '' }}>Senior Level</option>
                                <option value="executive" {{ old('experience_level', $job->experience_level) == 'executive' ? 'selected' : '' }}>Executive</option>
                            </select>
                            @error('experience_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $job->location) }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Salary Range -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="salary_min" class="form-label">Minimum Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('salary_min') is-invalid @enderror" id="salary_min" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}">
                                </div>
                                @error('salary_min')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="salary_max" class="form-label">Maximum Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('salary_max') is-invalid @enderror" id="salary_max" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}">
                                </div>
                                @error('salary_max')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Deadline -->
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Application Deadline <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', $job->deadline->format('Y-m-d')) }}" required>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Job Description -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Job Details</h5>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description', $job->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Company Logo -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Company Logo</h5>
                        </div>

                        <div class="mb-3">
                            <label for="company_logo" class="form-label">Company Logo</label>

                            @if($job->company_logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $job->company_logo) }}" alt="Current Company Logo" class="img-thumbnail" style="max-height: 100px;">
                                    <p class="small text-muted mt-1">Current logo</p>
                                </div>
                            @endif

                            <input type="file" class="form-control @error('company_logo') is-invalid @enderror" id="company_logo" name="company_logo">
                            <div class="form-text">Upload a new logo to replace the current one. Leave empty to keep the current logo.</div>
                            @error('company_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please Note:</strong> Editing this job will reset its approval status. It will need to be approved by an administrator again before it becomes visible to candidates.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('employer.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Job
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
