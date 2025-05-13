<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Application;
use App\Notifications\NewJobPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with summary statistics
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get recent jobs with employer information
        $jobs = Job::with('employer')
            ->latest()
            ->take(5)
            ->get();

        // Get recent users
        $users = User::latest()
            ->take(5)
            ->get();

        // Get some statistics
        $stats = [
            'totalJobs' => Job::count(),
            'pendingJobs' => Job::where('is_approved', false)->count(),
            'totalUsers' => User::count(),
            'totalApplications' => Application::count(),
            'employerCount' => User::where('role', 'employer')->count(),
            'candidateCount' => User::where('role', 'candidate')->count(),
        ];

        return view('admin.dashboard', compact('jobs', 'users', 'stats'));
    }

    /**
     * Display a list of all users for management
     *
     * @return \Illuminate\View\View
     */
    public function manageUsers()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.manage', compact('users'));
    }

    /**
     * Show the form for editing a user
     *
     * @param int $id The user ID
     * @return \Illuminate\View\View
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user's information
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The user ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string|in:admin,employer,candidate',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users.manage')
            ->with('success', "User '{$user->name}' has been updated successfully.");
    }

    /**
     * Delete a user
     *
     * @param int $id The user ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.manage')
                ->with('error', "You cannot delete your own account.");
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.manage')
            ->with('success', "User '{$userName}' has been deleted successfully.");
    }

    /**
     * Display a list of pending jobs awaiting approval
     *
     * @return \Illuminate\View\View
     */
    public function pendingJobs()
    {
        $jobs = Job::where('is_approved', false)
            ->with('employer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.jobs.pending', compact('jobs'));
    }

    /**
     * Approve a job listing
     *
     * @param int $id The job ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveJob($id)
    {
        $job = Job::with('employer')->findOrFail($id);
        $job->is_approved = true;
        $job->save();

        // Notify candidates who might be interested in this job category
        $interestedCandidates = User::where('role', 'candidate')
            ->where(function($query) use ($job) {
                // This is a simplified approach. In a real app, you might have a user_preferences table
                // or use a more sophisticated matching algorithm
                $query->where('skills', 'like', "%{$job->category}%")
                      ->orWhere('bio', 'like', "%{$job->category}%");
            })
            ->get();

        Notification::send($interestedCandidates, new NewJobPosted($job));

        return redirect()->route('admin.jobs.pending')
            ->with('success', "Job '{$job->title}' has been approved and is now visible to candidates.");
    }

    /**
     * Reject a job listing
     *
     * @param int $id The job ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectJob($id)
    {
        $job = Job::findOrFail($id);
        $jobTitle = $job->title;

        // Since we only have a boolean is_approved field, we'll just delete rejected jobs
        // Alternatively, you could add a 'rejected' status column to the job_posts table
        $job->delete();

        return redirect()->route('admin.jobs.pending')
            ->with('success', "Job '{$jobTitle}' has been rejected and removed from the system.");
    }
}