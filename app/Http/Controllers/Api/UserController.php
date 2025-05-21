<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Get users by role
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByRole(Request $request)
    {
        try {
            $role = $request->query('role');
            
            if (!$role) {
                return response()->json([]);
            }
            
            Log::info('Getting users with role: ' . $role);
            
            $users = User::where('role', $role)->get(['id', 'name']);
            
            Log::info('Found ' . $users->count() . ' users with role: ' . $role);
            
            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error getting users by role: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get users: ' . $e->getMessage()], 500);
        }
    }
}
