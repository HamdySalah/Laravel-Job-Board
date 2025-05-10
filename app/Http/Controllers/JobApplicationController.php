<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Display all applications for a specific job (Employer view)
     *
     * @param int $jobId The job ID
     * @return \Illuminate\View\View
     */
    public function viewApplications($jobId)
    {
        // Ensure the job belongs to the current employer
        $job = Job::where('id', $jobId)
            ->where('employer_id', Auth::id())
            ->firstOrFail();

        $applications = Application::where('job_id', $job->id)
            ->with('candidate')
            ->latest()
            ->get();

        return view('employer.job-applications', compact('job', 'applications'));
    }

    /**
     * Update the status of a job application (Employer action)
     *
     * @param \Illuminate\Http\Request $request
     * @param int $applicationId The application ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $applicationId)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,interviewed,accepted,rejected'
        ]);

        $application = Application::findOrFail($applicationId);
        $job = Job::findOrFail($application->job_id);

        // Ensure the job belongs to the current employer
        if ($job->employer_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $oldStatus = $application->status;
        $application->status = $request->status;
        $application->save();

        return back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Display all job applications for the current candidate
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $applications = Application::where('candidate_id', Auth::id())
            ->with(['job' => function($query) {
                $query->with('employer');
            }])
            ->latest()
            ->get();

        return view('candidate.job-applications', compact('applications'));
    }

    /**
     * Submit a job application (Candidate action)
     *
     * @param int $jobId The job ID
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function apply($jobId, Request $request = null)
    {
        $job = Job::findOrFail($jobId);

        // Check if job is approved
        if (!$job->is_approved) {
            return back()->with('error', 'This job is not available for applications.');
        }

        // Check if already applied
        $existingApplication = Application::where('job_id', $job->id)
            ->where('candidate_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'You have already applied for this job.');
        }

        // Check if candidate has a resume
        $candidate = Auth::user();
        if (!$candidate->resume_path) {
            return redirect()->route('candidate.profile')
                ->with('error', 'Please upload your resume before applying for jobs.');
        }

        // Get cover letter if provided
        $message = $request ? $request->input('message') : null;

        // Create application
        $application = Application::create([
            'job_id' => $job->id,
            'candidate_id' => Auth::id(),
            'resume_path' => $candidate->resume_path,
            'message' => $message,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Your application has been submitted successfully.');
    }

    /**
     * Process application with uploaded resume (Alternative method)
     *
     * @param \Illuminate\Http\Request $request
     * @param int $jobId The job ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $jobId)
    {
        $job = Job::findOrFail($jobId);

        $request->validate([
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'message' => 'nullable|string|max:1000',
        ]);

        // Check if already applied
        $existingApplication = Application::where('job_id', $job->id)
            ->where('candidate_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'You have already applied for this job.');
        }

        // Process resume
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');

            // Update user's resume path
            $user = Auth::user();
            $user->resume_path = $resumePath;
            $user->save();
        } else {
            $resumePath = Auth::user()->resume_path;
        }

        if (!$resumePath) {
            return back()->with('error', 'Please upload a resume to apply for this job.');
        }

        // Create application
        Application::create([
            'job_id' => $job->id,
            'candidate_id' => Auth::id(),
            'resume_path' => $resumePath,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Your application has been submitted successfully.');
    }

    /**
     * Cancel a job application (Candidate action)
     *
     * @param int $applicationId The application ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($applicationId)
    {
        $application = Application::where('id', $applicationId)
            ->where('candidate_id', Auth::id())
            ->firstOrFail();

        // Only allow cancellation if status is pending or reviewing
        if (!in_array($application->status, ['pending', 'reviewing'])) {
            return back()->with('error', 'You cannot cancel this application because it has already been processed.');
        }

        $application->delete();
        return back()->with('success', 'Your application has been canceled successfully.');
    }
}
?>