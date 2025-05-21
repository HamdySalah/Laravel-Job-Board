@extends('layouts.main')

@section('title', 'Chat')

@push('styles')
<style>
    .chat-container {
        height: 70vh;
        display: flex;
        flex-direction: column;
    }

    .chat-sidebar {
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
    }

    .chat-input {
        border-top: 1px solid #dee2e6;
        padding: 1rem;
        background-color: #fff;
    }

    .chat-header {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }

    .user-list {
        overflow-y: auto;
        max-height: calc(70vh - 72px);
    }

    .user-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #dee2e6;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .user-item:hover, .user-item.active {
        background-color: #e9ecef;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #6c757d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 0.75rem;
    }

    .message {
        max-width: 75%;
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        position: relative;
    }

    .message-sent {
        align-self: flex-end;
        background-color: #0d6efd;
        color: white;
        border-bottom-right-radius: 0.25rem;
    }

    .message-received {
        align-self: flex-start;
        background-color: #e9ecef;
        border-bottom-left-radius: 0.25rem;
    }

    .message-time {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 0.25rem;
        text-align: right;
    }

    .typing-indicator {
        padding: 0.5rem;
        background-color: #e9ecef;
        border-radius: 1rem;
        margin-bottom: 1rem;
        align-self: flex-start;
        display: none;
    }

    .typing-indicator span {
        height: 0.5rem;
        width: 0.5rem;
        float: left;
        margin: 0 1px;
        background-color: #9E9EA1;
        display: block;
        border-radius: 50%;
        opacity: 0.4;
    }

    .typing-indicator span:nth-of-type(1) {
        animation: 1s blink infinite 0.3333s;
    }

    .typing-indicator span:nth-of-type(2) {
        animation: 1s blink infinite 0.6666s;
    }

    .typing-indicator span:nth-of-type(3) {
        animation: 1s blink infinite 0.9999s;
    }

    @keyframes blink {
        50% {
            opacity: 1;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="chat-container">
                        <div class="row g-0 h-100">
                            <!-- Chat Sidebar -->
                            <div class="col-md-4 col-lg-3 chat-sidebar">
                                <div class="chat-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Conversations</h5>
                                    <button class="btn btn-sm btn-outline-primary" id="newChatBtn" data-bs-toggle="modal" data-bs-target="#newChatModal">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="user-list" id="userList">
                                    <!-- User list will be populated dynamically -->
                                </div>
                            </div>

                            <!-- Chat Main Area -->
                            <div class="col-md-8 col-lg-9 d-flex flex-column">
                                <div class="chat-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" id="currentChatAvatar">?</div>
                                        <div>
                                            <h5 class="mb-0" id="currentChatName">Select a conversation</h5>
                                            <small class="text-muted" id="currentChatStatus">No active chat</small>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary me-1" id="refreshBtn">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        @if(app()->environment('local'))
                                            <a href="{{ route('chat.debug') }}" class="btn btn-sm btn-outline-info" title="Debug Chat">
                                                <i class="fas fa-bug"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="chat-messages" id="chatMessages">
                                    <div class="text-center my-auto">
                                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                        <p>Select a conversation to start chatting</p>
                                    </div>

                                    <div class="typing-indicator" id="typingIndicator">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>

                                <div class="chat-input">
                                    <form id="messageForm" class="d-flex">
                                        <input type="text" class="form-control me-2" id="messageInput" placeholder="Type a message..." disabled>
                                        <button type="submit" class="btn btn-primary" id="sendBtn" disabled>
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- New Chat Modal -->
    <div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newChatModalLabel">Start a New Conversation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userSelect" class="form-label">Select a user to chat with:</label>
                        <select class="form-select" id="userSelect">
                            <option value="">Loading users...</option>
                        </select>
                    </div>
                    <div id="userSelectError" class="text-danger" style="display: none;">
                        Please select a user to start a conversation.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="startChatBtn">Start Conversation</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3/dist/web/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        let currentChatId = null;
        let currentChatUser = null;
        const userId = {{ Auth::id() }};

        // DOM elements
        const userList = document.getElementById('userList');
        const chatMessages = document.getElementById('chatMessages');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const currentChatName = document.getElementById('currentChatName');
        const currentChatStatus = document.getElementById('currentChatStatus');
        const currentChatAvatar = document.getElementById('currentChatAvatar');
        const typingIndicator = document.getElementById('typingIndicator');
        const refreshBtn = document.getElementById('refreshBtn');

        // Initialize Pusher
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        // Subscribe to the user's channel
        const channel = pusher.subscribe('user.' + userId);

        // Listen for new messages
        channel.bind('new-message', function(data) {
            if (currentChatId === data.conversation_id) {
                addMessage(data.message, false);
            }

            // Update the user list to show new message indicator
            loadUserList();
        });

        // Listen for typing indicator
        channel.bind('typing', function(data) {
            if (currentChatId === data.conversation_id) {
                showTypingIndicator();

                // Hide typing indicator after 3 seconds
                setTimeout(hideTypingIndicator, 3000);
            }
        });

        // Load user list on page load
        loadUserList();

        // Get modal elements
        const newChatModal = document.getElementById('newChatModal');
        const userSelect = document.getElementById('userSelect');
        const startChatBtn = document.getElementById('startChatBtn');
        const userSelectError = document.getElementById('userSelectError');

        // Event listeners
        messageForm.addEventListener('submit', sendMessage);
        messageInput.addEventListener('input', sendTypingIndicator);
        refreshBtn.addEventListener('click', loadUserList);
        startChatBtn.addEventListener('click', startNewConversation);

        // Load available users when modal is shown
        newChatModal.addEventListener('show.bs.modal', loadAvailableUsers);

        // Functions
        function loadUserList() {
            fetch('/api/chat/conversations')
                .then(response => response.json())
                .then(data => {
                    userList.innerHTML = '';

                    if (data.length === 0) {
                        userList.innerHTML = '<div class="p-3 text-center text-muted">No conversations yet</div>';
                        return;
                    }

                    data.forEach(conversation => {
                        const otherUser = conversation.users.find(user => user.id !== userId);
                        const hasUnread = conversation.unread_count > 0;

                        const userItem = document.createElement('div');
                        userItem.className = `user-item d-flex align-items-center ${currentChatId === conversation.id ? 'active' : ''}`;
                        userItem.dataset.id = conversation.id;
                        userItem.dataset.userId = otherUser.id;
                        userItem.dataset.name = otherUser.name;

                        userItem.innerHTML = `
                            <div class="user-avatar">${otherUser.name.charAt(0)}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">${otherUser.name}</h6>
                                    ${hasUnread ? '<span class="badge bg-primary rounded-pill">' + conversation.unread_count + '</span>' : ''}
                                </div>
                                <small class="text-muted">${conversation.last_message ? conversation.last_message.substring(0, 20) + (conversation.last_message.length > 20 ? '...' : '') : 'No messages yet'}</small>
                            </div>
                        `;

                        userItem.addEventListener('click', () => loadChat(conversation.id, otherUser));
                        userList.appendChild(userItem);
                    });
                })
                .catch(error => console.error('Error loading conversations:', error));
        }

        function loadChat(conversationId, user) {
            currentChatId = conversationId;
            currentChatUser = user;

            // Update UI
            currentChatName.textContent = user.name;
            currentChatStatus.textContent = 'Online';
            currentChatAvatar.textContent = user.name.charAt(0);

            // Enable input
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();

            // Update active user
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.id === conversationId.toString()) {
                    item.classList.add('active');
                }
            });

            // Load messages
            fetch(`/api/chat/conversations/${conversationId}/messages`)
                .then(response => response.json())
                .then(data => {
                    chatMessages.innerHTML = '';

                    if (data.length === 0) {
                        chatMessages.innerHTML = '<div class="text-center my-auto"><p>No messages yet. Start the conversation!</p></div>';
                        return;
                    }

                    data.forEach(message => {
                        addMessage(message, message.user_id === userId);
                    });

                    // Scroll to bottom
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    // Mark as read
                    fetch(`/api/chat/conversations/${conversationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        function sendMessage(e) {
            e.preventDefault();

            if (!currentChatId || !messageInput.value.trim()) return;

            const message = messageInput.value.trim();

            // Clear input
            messageInput.value = '';

            // Send message to server
            fetch('/api/chat/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    conversation_id: currentChatId,
                    content: message
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Message sent successfully:', data);

                // Add message to UI after successful send
                addMessage({
                    content: message,
                    created_at: new Date().toISOString()
                }, true);

                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            });
        }

        function sendTypingIndicator() {
            if (!currentChatId) return;

            fetch('/api/chat/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    conversation_id: currentChatId,
                    recipient_id: currentChatUser.id
                })
            })
            .catch(error => console.error('Error sending typing indicator:', error));
        }

        function addMessage(message, isSent) {
            // Remove empty state if present
            const emptyState = chatMessages.querySelector('.text-center');
            if (emptyState) {
                chatMessages.innerHTML = '';
            }

            // Create message element
            const messageEl = document.createElement('div');
            messageEl.className = `message ${isSent ? 'message-sent' : 'message-received'}`;

            // Format date
            const date = new Date(message.created_at);
            const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            messageEl.innerHTML = `
                <div>${message.content}</div>
                <div class="message-time">${formattedTime}</div>
            `;

            // Add before typing indicator
            if (typingIndicator.parentNode === chatMessages) {
                chatMessages.insertBefore(messageEl, typingIndicator);
            } else {
                chatMessages.appendChild(messageEl);
            }

            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTypingIndicator() {
            typingIndicator.style.display = 'block';
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function hideTypingIndicator() {
            typingIndicator.style.display = 'none';
        }

        function loadAvailableUsers() {
            // Reset the select element
            userSelect.innerHTML = '<option value="">Loading users...</option>';
            userSelectError.style.display = 'none';

            // Get the current user's role
            const currentUserRole = '{{ Auth::user()->role }}';
            const targetRole = currentUserRole === 'employer' ? 'candidate' : 'employer';

            console.log('Loading users with role:', targetRole);

            // Fetch users with the opposite role
            // Use the debug route in local environment for easier debugging
            const url = '{{ app()->environment('local') ? route('chat.debug.users') : '/api/users?role=' . urlencode('TARGET_ROLE') }}'.replace('TARGET_ROLE', targetRole);
            console.log('Fetching users from URL:', url);

            fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Users loaded:', data);
                    userSelect.innerHTML = '<option value="">Select a user</option>';

                    // Handle both formats: direct array or debug format with users property
                    const users = data.users ? data.users : data;

                    if (!users || users.length === 0) {
                        userSelect.innerHTML = '<option value="">No users available</option>';
                        return;
                    }

                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name;
                        userSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    userSelect.innerHTML = '<option value="">Error loading users</option>';
                });
        }

        function startNewConversation() {
            const selectedUserId = userSelect.value;

            if (!selectedUserId) {
                userSelectError.style.display = 'block';
                return;
            }

            userSelectError.style.display = 'none';

            console.log('Starting new conversation with user ID:', selectedUserId);

            fetch('/api/chat/conversations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: selectedUserId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Conversation created:', data);

                if (data.success) {
                    // Close the modal
                    bootstrap.Modal.getInstance(newChatModal).hide();

                    // Reload the user list
                    loadUserList();

                    // Load the new conversation
                    setTimeout(() => {
                        const conversationId = data.conversation_id;
                        const userItems = document.querySelectorAll('.user-item');

                        console.log('Looking for conversation ID:', conversationId);
                        console.log('Available user items:', userItems.length);

                        let found = false;
                        for (const item of userItems) {
                            console.log('Item ID:', item.dataset.id);
                            if (item.dataset.id == conversationId) {
                                item.click();
                                found = true;
                                break;
                            }
                        }

                        if (!found) {
                            console.warn('Conversation created but not found in the list. Reloading page...');
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    console.error('Failed to start conversation:', data);
                    alert(data.message || 'Failed to start conversation');
                }
            })
            .catch(error => {
                console.error('Error starting conversation:', error);
                alert('Failed to start conversation. Please try again. Error: ' + error.message);
            });
        }
    });
</script>
@endpush
