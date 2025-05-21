<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{
    /**
     * Display the chat interface
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('chat.index');
    }

    /**
     * Get all conversations for the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversations()
    {
        $user = Auth::user();

        // Get conversations where the user is either employer or candidate
        $conversations = Conversation::where(function($query) use ($user) {
                $query->where('employer_id', $user->id)
                      ->orWhere('candidate_id', $user->id);
            })
            ->with(['employer', 'candidate'])
            ->get()
            ->map(function($conversation) use ($user) {
                // Determine the other user in the conversation
                $otherUser = $conversation->employer_id == $user->id
                    ? $conversation->candidate
                    : $conversation->employer;

                // Get the last message
                $lastMessage = $conversation->messages()->latest()->first();

                // Count unread messages
                $unreadCount = $conversation->messages()
                    ->where('sender_id', '!=', $user->id)
                    ->where('read', false)
                    ->count();

                return [
                    'id' => $conversation->id,
                    'users' => [$otherUser],
                    'unread_count' => $unreadCount,
                    'last_message' => $lastMessage ? $lastMessage->body : null,
                    'updated_at' => $conversation->updated_at
                ];
            })
            ->sortByDesc('updated_at')
            ->values();

        return response()->json($conversations);
    }

    /**
     * Get messages for a specific conversation
     *
     * @param int $conversationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages($conversationId)
    {
        $user = Auth::user();

        // Check if user is part of the conversation
        $conversation = Conversation::where(function($query) use ($user) {
            $query->where('employer_id', $user->id)
                  ->orWhere('candidate_id', $user->id);
        })->findOrFail($conversationId);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'user_id' => $message->sender_id,
                    'content' => $message->body,
                    'read' => $message->read ?? false,
                    'created_at' => $message->created_at
                ];
            });

        return response()->json($messages);
    }

    /**
     * Send a new message
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'content' => 'required|string'
            ]);

            $user = Auth::user();

            // Check if user is part of the conversation
            $conversation = Conversation::where(function($query) use ($user) {
                $query->where('employer_id', $user->id)
                      ->orWhere('candidate_id', $user->id);
            })->findOrFail($request->conversation_id);

            // Create message
            $message = new Message([
                'sender_id' => $user->id,
                'conversation_id' => $conversation->id,
                'body' => $request->content,
                'read' => false
            ]);

            $message->save();

            // Update conversation timestamp
            $conversation->touch();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'user_id' => $message->sender_id,
                    'content' => $message->body,
                    'created_at' => $message->created_at
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all messages in a conversation as read
     *
     * @param int $conversationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($conversationId)
    {
        $user = Auth::user();

        // Check if user is part of the conversation
        $conversation = Conversation::where(function($query) use ($user) {
            $query->where('employer_id', $user->id)
                  ->orWhere('candidate_id', $user->id);
        })->findOrFail($conversationId);

        // Mark all unread messages from other users as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Send typing indicator
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTypingIndicator(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'recipient_id' => 'required|exists:users,id'
        ]);

        $user = Auth::user();

        // Check if user is part of the conversation
        Conversation::where(function($query) use ($user) {
            $query->where('employer_id', $user->id)
                  ->orWhere('candidate_id', $user->id);
        })->findOrFail($request->conversation_id);

        // We're not implementing real-time typing indicators for now
        // This would require setting up Pusher or another WebSocket service

        return response()->json(['success' => true]);
    }

    /**
     * Start a new conversation with a user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = Auth::user();
        $otherUser = User::findOrFail($request->user_id);

        // Determine if the current user is an employer or candidate
        $isEmployer = $user->role === 'employer';
        $isCandidate = $user->role === 'candidate';

        // The other user should have the opposite role
        $otherIsEmployer = $otherUser->role === 'employer';
        $otherIsCandidate = $otherUser->role === 'candidate';

        // Check if they have compatible roles (employer-candidate or candidate-employer)
        if (!($isEmployer && $otherIsCandidate) && !($isCandidate && $otherIsEmployer)) {
            return response()->json([
                'success' => false,
                'message' => 'Conversations can only be started between employers and candidates'
            ], 400);
        }

        // Check if conversation already exists
        $existingConversation = Conversation::where(function($query) use ($user, $otherUser) {
            $query->where(function($q) use ($user, $otherUser) {
                $q->where('employer_id', $user->id)
                  ->where('candidate_id', $otherUser->id);
            })->orWhere(function($q) use ($user, $otherUser) {
                $q->where('employer_id', $otherUser->id)
                  ->where('candidate_id', $user->id);
            });
        })->first();

        if ($existingConversation) {
            return response()->json([
                'success' => true,
                'conversation_id' => $existingConversation->id
            ]);
        }

        // Create new conversation
        $conversation = new Conversation();

        if ($isEmployer) {
            $conversation->employer_id = $user->id;
            $conversation->candidate_id = $otherUser->id;
        } else {
            $conversation->employer_id = $otherUser->id;
            $conversation->candidate_id = $user->id;
        }

        $conversation->save();

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id
        ]);
    }
}
