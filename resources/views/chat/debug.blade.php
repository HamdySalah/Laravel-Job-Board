@extends('layouts.main')

@section('title', 'Chat Debug')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Chat Debug Information</h5>
                </div>
                <div class="card-body">
                    <h6>Current User</h6>
                    <pre class="bg-light p-3 mb-4">
ID: {{ $user->id }}
Name: {{ $user->name }}
Email: {{ $user->email }}
Role: {{ $user->role }}
                    </pre>

                    <h6>Conversations ({{ $conversations->count() }})</h6>
                    @if($conversations->count() > 0)
                        @foreach($conversations as $conversation)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong>Conversation ID: {{ $conversation->id }}</strong>
                                </div>
                                <div class="card-body">
                                    <p><strong>Employer:</strong> {{ $conversation->employer->name }} (ID: {{ $conversation->employer->id }})</p>
                                    <p><strong>Candidate:</strong> {{ $conversation->candidate->name }} (ID: {{ $conversation->candidate->id }})</p>
                                    <p><strong>Created:</strong> {{ $conversation->created_at->format('Y-m-d H:i:s') }}</p>
                                    <p><strong>Updated:</strong> {{ $conversation->updated_at->format('Y-m-d H:i:s') }}</p>
                                    
                                    <h6 class="mt-3">Messages ({{ $conversation->messages->count() }})</h6>
                                    @if($conversation->messages->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Sender</th>
                                                        <th>Message</th>
                                                        <th>Read</th>
                                                        <th>Created</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($conversation->messages as $message)
                                                        <tr>
                                                            <td>{{ $message->id }}</td>
                                                            <td>{{ $message->sender->name }}</td>
                                                            <td>{{ $message->body }}</td>
                                                            <td>{{ $message->read ? 'Yes' : 'No' }}</td>
                                                            <td>{{ $message->created_at->format('Y-m-d H:i:s') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">No messages in this conversation.</div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <form action="{{ route('api.chat.messages') }}" method="POST" class="d-flex">
                                            @csrf
                                            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                                            <input type="text" name="content" class="form-control me-2" placeholder="Type a test message...">
                                            <button type="submit" class="btn btn-primary">Send</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">No conversations found.</div>
                    @endif
                    
                    <div class="mt-4">
                        <h6>Create a New Conversation</h6>
                        <form action="{{ route('api.chat.conversations') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Select User</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Select a user</option>
                                    @if($user->role === 'employer')
                                        @foreach(\App\Models\User::where('role', 'candidate')->get() as $candidate)
                                            <option value="{{ $candidate->id }}">{{ $candidate->name }} (Candidate)</option>
                                        @endforeach
                                    @else
                                        @foreach(\App\Models\User::where('role', 'employer')->get() as $employer)
                                            <option value="{{ $employer->id }}">{{ $employer->name }} (Employer)</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Create Conversation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('chat.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Chat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
