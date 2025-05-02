<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

Route::resource('jobs', JobApplicationController::class);


require __DIR__.'/auth.php';
