<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobListingController extends Controller
{
    /**
     * Display a listing of job listings
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
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
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('job-listings.create');
    }

    /**
     * Store a newly created job listing
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'type' => 'required|string|in:full-time,part-time,remote,contract,internship',
            'experience_level' => 'nullable|string|in:entry,mid,senior,executive',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'required|date|after:today',
        ]);

        // Handle company logo upload if provided
        $companyLogo = null;
        if ($request->hasFile('company_logo')) {
            $request->validate([
                'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $companyLogo = $request->file('company_logo')->store('company_logos', 'public');
        }

        $job = Job::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'category' => $request->category,
            'type' => $request->type,
            'experience_level' => $request->experience_level,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'deadline' => $request->deadline,
            'is_approved' => false,
            'employer_id' => Auth::id(),
            'company_logo' => $companyLogo,
        ]);

        return redirect()->route('employer.dashboard')
            ->with('success', 'Your job listing has been submitted and is pending approval.');
    }

    /**
     * Show the form for editing a job listing
     *
     * @param int $id The job ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $job = Job::findOrFail($id);

        // Ensure the job belongs to the current employer
        if ($job->employer_id !== Auth::id()) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'You are not authorized to edit this job listing.');
        }

        return view('job-listings.edit', compact('job'));
    }

    /**
     * Update a job listing
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The job ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'type' => 'required|string|in:full-time,part-time,remote,contract,internship',
            'experience_level' => 'nullable|string|in:entry,mid,senior,executive',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'required|date|after:today',
        ]);

        $job = Job::findOrFail($id);

        // Ensure the job belongs to the current employer
        if ($job->employer_id !== Auth::id()) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'You are not authorized to update this job listing.');
        }

        // Handle company logo upload if provided
        if ($request->hasFile('company_logo')) {
            $request->validate([
                'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Delete old logo if exists
            if ($job->company_logo) {
                Storage::disk('public')->delete($job->company_logo);
            }

            $companyLogo = $request->file('company_logo')->store('company_logos', 'public');
            $job->company_logo = $companyLogo;
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
    }

    /**
     * Display a job listing
     *
     * @param int $id The job ID
     * @return \Illuminate\View\View
     */
    public function show($id)
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
     *
     * @param int $id The job ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $job = Job::findOrFail($id);

        // Ensure the job belongs to the current employer
        if ($job->employer_id !== Auth::id()) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'You are not authorized to delete this job listing.');
        }

        // Delete company logo if exists
        if ($job->company_logo) {
            Storage::disk('public')->delete($job->company_logo);
        }

        $job->delete();

        return redirect()->route('employer.dashboard')
            ->with('success', 'Your job listing has been deleted successfully.');
    }
}