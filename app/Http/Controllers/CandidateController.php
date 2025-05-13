<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Display the candidate dashboard with their applications
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get the candidate's job applications with job details
        $applications = Application::where('candidate_id', Auth::id())
            ->with(['job' => function($query) {
                $query->with('employer');
            }])
            ->latest()
            ->get();

        // Get some recommended jobs based on the candidate's applications and skills
        $appliedJobIds = $applications->pluck('job_id')->toArray();
        $candidate = Auth::user();

        // Start with a base query for approved jobs that the candidate hasn't applied to
        $recommendedJobsQuery = Job::where('is_approved', true)
            ->whereNotIn('id', $appliedJobIds);

        // If the candidate has applied to jobs, recommend similar ones based on categories
        if (count($appliedJobIds) > 0) {
            // Get categories from jobs the candidate has applied to
            $appliedCategories = Job::whereIn('id', $appliedJobIds)
                ->pluck('category')
                ->unique()
                ->toArray();

            if (!empty($appliedCategories)) {
                $recommendedJobsQuery->whereIn('category', $appliedCategories);
            }
        }

        // If the candidate has skills listed, try to match them with job descriptions
        if ($candidate->skills) {
            $skills = explode(',', $candidate->skills);
            $skillsQuery = Job::where('is_approved', true)
                ->whereNotIn('id', $appliedJobIds);

            foreach ($skills as $skill) {
                $skill = trim($skill);
                if (!empty($skill)) {
                    $skillsQuery->orWhere('title', 'like', "%{$skill}%")
                        ->orWhere('description', 'like', "%{$skill}%");
                }
            }

            // Merge skill-based recommendations with category-based ones
            $skillBasedJobs = $skillsQuery->take(2)->get();
            $categoryBasedJobs = $recommendedJobsQuery->take(4)->get();

            // Combine and remove duplicates
            $recommendedJobs = $categoryBasedJobs->merge($skillBasedJobs)
                ->unique('id')
                ->take(4);
        } else {
            // If no skills are listed, just use category-based recommendations
            $recommendedJobs = $recommendedJobsQuery->latest()->take(4)->get();
        }

        // If we still don't have enough recommendations, add some recent jobs
        if ($recommendedJobs->count() < 4) {
            $moreJobs = Job::where('is_approved', true)
                ->whereNotIn('id', $appliedJobIds)
                ->whereNotIn('id', $recommendedJobs->pluck('id')->toArray())
                ->latest()
                ->take(4 - $recommendedJobs->count())
                ->get();

            $recommendedJobs = $recommendedJobs->merge($moreJobs);
        }

        return view('candidate.dashboard', compact('applications', 'recommendedJobs'));
    }

    /**
     * Show the candidate's profile
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $candidate = Auth::user();
        return view('candidate.profile', compact('candidate'));
    }

    /**
     * Update the candidate's profile information
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
            'experience' => 'nullable|string',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $candidate = Auth::user();

        // Update basic profile information - exclude phone and location to avoid errors
        $candidate->fill($request->only([
            'name', 'email', 'skills', 'experience', 'bio'
        ]));

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($candidate->profile_picture) {
                Storage::disk('public')->delete($candidate->profile_picture);
            }

            // Store new profile picture
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $candidate->profile_picture = $profilePicturePath;
        }

        // Handle resume upload
        if ($request->hasFile('resume')) {
            // Delete old resume if exists
            if ($candidate->resume_path) {
                Storage::disk('public')->delete($candidate->resume_path);
            }

            // Store new resume
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $candidate->resume_path = $resumePath;
        }

        $candidate->save();

        return redirect()->route('candidate.profile')
            ->with('success', 'Your profile has been updated successfully.');
    }

    /**
     * Show the candidate's job applications
     *
     * @return \Illuminate\View\View
     */
    public function jobApplications()
    {
        $applications = Application::where('candidate_id', Auth::id())
            ->with(['job' => function($query) {
                $query->with('employer');
            }])
            ->latest()
            ->get();

        return view('candidate.job-applications', compact('applications'));
    }
}
