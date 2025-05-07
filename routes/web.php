<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/jobs', function () {
    return view('jobs.index');
})->middleware(['auth', 'verified'])->name('jobs.index');

Route::get('/employers', function () {
    return view('employers.details');
})->middleware(['auth', 'verified'])->name('employers.details');

Route::get('/candidates', function () {
    return view('candidates.index');
})->middleware(['auth', 'verified'])->name('candidates.index');

Route::get('/candidate-details', function () {
    return view('candidates.details');
})->middleware(['auth', 'verified'])->name('candidates.details');

Route::get('/blog', function () {
    return view('blog');
})->middleware(['auth', 'verified'])->name('blog');

Route::get('/pages', function () {
    return view('pages');
})->middleware(['auth', 'verified'])->name('pages');

Route::get('/contact', function () {
    return view('contact');
})->middleware(['auth', 'verified'])->name('contact');

Route::get('/about-us', function () {
    return view('about-us');
})->middleware(['auth', 'verified'])->name('about.us');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('job-listings', JobListingController::class);
    Route::post('job-listings/{jobListing}/apply', [JobApplicationController::class, 'store'])->name('job-listings.apply');
    Route::get('job-applications', [JobApplicationController::class, 'index'])->name('job-applications.index');
    Route::delete('job-applications/{application}', [JobApplicationController::class, 'destroy'])->name('job-applications.destroy');
    Route::put('job-listings/{jobListing}/approve', [AdminController::class, 'approve'])->name('job-listings.approve');
    Route::put('job-listings/{jobListing}/reject', [AdminController::class, 'reject'])->name('job-listings.reject');
});

require __DIR__.'/auth.php';