@extends('layouts.app')

@section('content')
    <div class="container" style="color:white">
        <h1>Available Jobs</h1>

        @foreach($jobs as $job)
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title">{{ $job->title }}</h3>
                    <p class="card-text">{{ Str::limit($job->description, 150) }}</p>
                    <p><strong>Location:</strong> {{ $job->location }}</p>
                    <p><strong>Type:</strong> {{ $job->type }}</p>
                    <p><strong>Deadline:</strong> {{ $job->deadline }}</p>
                    <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-primary">View Job</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
