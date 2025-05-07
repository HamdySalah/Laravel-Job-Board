<?php

namespace App\Http\Controllers;
use App\Models\Job;

use Illuminate\Http\Request;

class JobListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::latest()->get();
        return view('jobs.index', compact('jobs'));
        // return "Iam in the index method of JobApplicationController";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'deadline' => 'required|date',
            'category' => 'required|string|max:255',
            'employer_id' => 'required|exists:users,id', 
            'company_logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('company_logos', 'public');
        } else {
            $logoPath = null;
        }
        Job::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'type' => $validated['type'],
            'salary_min' => $validated['salary_min'],
            'salary_max' => $validated['salary_max'],
            'deadline' => $validated['deadline'],
            'category' => $validated['category'],
            'employer_id' => $validated['employer_id'],
            'company_logo' => $logoPath,
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Job::findOrFail($id);
        return view('jobs.show',compact('job'));
          }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
            $job = Job::findOrFail($id);
            return view('jobs.edit', compact('job'));
            }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $job = Job::findOrFail($id);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'location' => 'required|string|max:255',
        'type' => 'required|string|max:255',
        'salary_min' => 'nullable|numeric',
        'salary_max' => 'nullable|numeric',
        'deadline' => 'required|date',
        'category' => 'required|string|max:255',
        'employer_id' => 'required|exists:users,id',
        'company_logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
    ]);

    if ($request->hasFile('company_logo')) {

        if ($job->company_logo && \Storage::disk('public')->exists($job->company_logo)) {
            \Storage::disk('public')->delete($job->company_logo);
        }

        $logoPath = $request->file('company_logo')->store('company_logos', 'public');
        $validated['company_logo'] = $logoPath;
    }

    $job->update($validated);

    return redirect()->route('jobs.show', $job->id)
        ->with('success', 'Job updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $job = Job::findOrFail($id);

    if ($job->company_logo && \Storage::disk('public')->exists($job->company_logo)) {
        \Storage::disk('public')->delete($job->company_logo);
    }

    $job->delete();

    return redirect()->route('jobs.index')
        ->with('success', 'Job deleted successfully.');
}

}
