<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Notifications\NewJobCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JobListingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of job listings
     */
    public function index(Request $request): View
    {
        $query = Job::where('is_approved', true);

        // Apply search filters if provided
        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by location
        if ($request->has('location') && $request->input('location')) {
            $location = $request->input('location');
            $query->where('location', 'like', "%{$location}%");
        }

        // Filter by category
        if ($request->has('category') && $request->input('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by job type
        if ($request->has('type') && $request->input('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by experience level
        if ($request->has('experience') && $request->input('experience')) {
            $experience = $request->input('experience');
            $query->where('experience_level', $experience);
        }

        // Filter by salary range
        if ($request->has('salary_min') && $request->input('salary_min')) {
            $salaryMin = $request->input('salary_min');
            $query->where('salary_max', '>=', $salaryMin);
        }

        if ($request->has('salary_max') && $request->input('salary_max')) {
            $salaryMax = $request->input('salary_max');
            $query->where('salary_min', '<=', $salaryMax);
        }

        // Filter by posted date
        if ($request->has('posted') && $request->input('posted')) {
            $daysAgo = $request->input('posted');
            $query->where('created_at', '>=', now()->subDays($daysAgo));
        }

        // Sort results
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');

        // Special handling for salary sorting
        if ($sortBy === 'salary_max') {
            $query->orderByRaw('COALESCE(salary_max, 0) DESC');
        } elseif ($sortBy === 'salary_min') {
            $query->orderByRaw('COALESCE(salary_min, 0) ASC');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $jobs = $query->with('employer')->paginate(10);

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
    public function store(Request $request): RedirectResponse
    {
        // Ensure user is authenticated and is an employer
        if (!Auth::check() || !Auth::user()->hasRole('employer')) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in as an employer to post a job.');
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'type' => 'required|string|in:full-time,part-time,remote,contract,internship',
            'experience_level' => 'nullable|string|in:entry,mid,senior,executive',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'required|date|after:today',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle company logo upload if provided
            $companyLogo = null;
            if ($request->hasFile('company_logo')) {
                $companyLogo = $request->file('company_logo')->store('company_logos', 'public');
            }

            // Create the new job listing
            $job = Job::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'category' => $validated['category'],
                'type' => $validated['type'],
                'experience_level' => $validated['experience_level'] ?? null,
                'salary_min' => $validated['salary_min'],
                'salary_max' => $validated['salary_max'],
                'deadline' => $validated['deadline'],
                'is_approved' => false, // Jobs require approval by default
                'employer_id' => Auth::id(),
                'company_logo' => $companyLogo,
            ]);

            // Load the employer relationship for notifications
            $job->load('employer');

            // Notify admins about the new job posting
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewJobCreated($job));

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
        $job = Job::findOrFail($id);
        $this->authorize('update', $job);

        return view('job-listings.edit', compact('job'));
    }

    /**
     * Update a job listing
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'type' => 'required|string|in:full-time,part-time,remote,contract,internship',
            'experience_level' => 'nullable|string|in:entry,mid,senior,executive',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'required|date|after:today',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $job = Job::findOrFail($id);
            $this->authorize('update', $job);

            // Handle company logo upload if provided
            if ($request->hasFile('company_logo')) {
                // Delete old logo if exists
                if ($job->company_logo) {
                    Storage::disk('public')->delete($job->company_logo);
                }
                $job->company_logo = $request->file('company_logo')->store('company_logos', 'public');
            }

            $job->update([
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'category' => $request->category,
                'type' => $request->type,
                'experience_level' => $request->experience_level,
                'salary_min' => $request->salary_min,
                'salary_max' => $request->salary_max,
                'deadline' => $request->deadline,
                // Reset approval status if job is edited
                'is_approved' => false,
            ]);

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
        $job = Job::with('employer')->findOrFail($id);

        // Get similar jobs based on category
        $similarJobs = Job::where('category', $job->category)
            ->where('id', '!=', $job->id)
            ->where('is_approved', true)
            ->take(3)
            ->get();

        return view('job-listings.show', compact('job', 'similarJobs'));
    }

    /**
     * Delete a job listing
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $job = Job::findOrFail($id);
            $this->authorize('delete', $job);

            // Delete company logo if exists
            if ($job->company_logo) {
                Storage::disk('public')->delete($job->company_logo);
            }

            $job->delete();

            return redirect()->route('employer.dashboard')
                ->with('success', 'Your job listing has been deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete job listing: ' . $e->getMessage());
        }
    }
}