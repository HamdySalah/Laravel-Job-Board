<?php

namespace App\Http\Controllers;

use App\DTOs\JobFilterDTO;
use App\Http\Requests\StoreJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JobListingController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly JobService $jobService
    ) {}

    /**
     * Display a listing of job listings
     */
    public function index(Request $request): View
    {
        $filters = JobFilterDTO::fromArray([
            ...$request->all(),
            'approved' => true, // Only show approved jobs to public
        ]);

        $jobs = $this->jobService->getPaginatedJobs($filters->toArray(), 10);

        // Get categories for filter dropdown
        $categories = Job::select('category')->distinct()->pluck('category');
        $jobTypes = ['full-time', 'part-time', 'remote', 'contract', 'internship'];

        return view('job-listings.index', compact('jobs', 'categories', 'jobTypes'));
    }

    /**
     * Show the form for creating a new job listing
     */
    public function create(): View|RedirectResponse
    {
        // Ensure user is authenticated and is an employer
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to post a job.');
        }

        if (!Auth::user()->hasRole('employer')) {
            return redirect()->route('home')
                ->with('error', 'Only employers can post jobs.');
        }

        // Get the authenticated employer
        $employer = Auth::user();

        // Define common job categories for suggestions
        $commonCategories = [
            'Web Development', 'Mobile Development', 'UI/UX Design',
            'Data Science', 'DevOps', 'Project Management',
            'Marketing', 'Sales', 'Customer Support', 'Finance',
            'Human Resources', 'Education', 'Healthcare', 'Engineering'
        ];

        return view('job-listings.create', compact('employer', 'commonCategories'));
    }

    /**
     * Store a newly created job listing
     */
    public function store(StoreJobRequest $request): RedirectResponse
    {
        try {
            $job = $this->jobService->createJob($request->validated(), Auth::user());

            return redirect()->route('employer.dashboard')
                ->with('success', 'Your job listing has been submitted and is pending approval.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create job listing: ' . $e->getMessage());
        }
    }



    /**
     * Show the form for editing a job listing
     */
    public function edit(int $id): View
    {
        $job = $this->jobService->findJobById($id);
        $this->authorize('update', $job);

        return view('job-listings.edit', compact('job'));
    }

    /**
     * Update a job listing
     */
    public function update(StoreJobRequest $request, int $id): RedirectResponse
    {
        try {
            $job = $this->jobService->findJobById($id);
            $this->authorize('update', $job);

            $this->jobService->updateJob($job, $request->validated());

            return redirect()->route('employer.dashboard')
                ->with('success', 'Your job listing has been updated and is pending approval.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update job listing: ' . $e->getMessage());
        }
    }

    /**
     * Display a job listing
     */
    public function show(int $id): View
    {
        $job = $this->jobService->findJobById($id);
        $similarJobs = $this->jobService->getSimilarJobs($job, 3);

        return view('job-listings.show', compact('job', 'similarJobs'));
    }

    /**
     * Delete a job listing
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $job = $this->jobService->findJobById($id);
            $this->authorize('delete', $job);

            $this->jobService->deleteJob($job);

            return redirect()->route('employer.dashboard')
                ->with('success', 'Your job listing has been deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete job listing: ' . $e->getMessage());
        }
    }
}