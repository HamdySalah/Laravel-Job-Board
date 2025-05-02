@extends('layouts.app')

@section('content')
<div class="container" style="color:white">
        <h2>{{ $job->title }}</h2>
        <p><strong>Description:</strong> {{ $job->description }}</p>
        <p><strong>Location:</strong> {{ $job->location }}</p>
        <p><strong>Type:</strong> {{ $job->type }}</p>
        <p><strong>Salary:</strong> {{ $job->salary_min }} - {{ $job->salary_max }}</p>
        <p><strong>Deadline:</strong> {{ $job->deadline }}</p>
        <p><strong>Category:</strong> {{ $job->category }}</p>
    </div>
@endsection