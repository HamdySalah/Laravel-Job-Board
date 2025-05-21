<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmployersViewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatController;
use App\Models\Conversation;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/welcome', function() {
    return view('welcome');
})->name('welcome');

// Test routes - remove these after testing
Route::get('/test', function() {
    return "This is a test route. If you can see this, routing is working.";
})->name('test');

Route::get('/test-about', function() {
    return view('about');
})->name('test-about');

Route::get('/test-controller', [App\Http\Controllers\TestController::class, 'index'])->name('test-controller');

// About and Contact routes
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Employers routes
Route::get('/employers', [EmployersViewController::class, 'index'])->name('employers.index');
Route::get('/employers/{id}', [EmployersViewController::class, 'show'])->name('employers.show');

// Job Listings (Public)
Route::get('/job-listings', [JobListingController::class, 'index'])
    ->name('job-listings.index');
Route::get('/job-listings/{id}', [JobListingController::class, 'show'])
    ->name('job-listings.show');

// Authentication Required Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware('redirect.role');

    // User Profile (All authenticated users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', function () {
            $user = Auth::user();

            // Redirect to role-specific profile page if available
            if ($user->role === 'admin') {
                return view('profile.admin-profile');
            } elseif ($user->role === 'employer') {
                return redirect()->route('employer.profile');
            } elseif ($user->role === 'candidate') {
                return redirect()->route('candidate.profile');
            } else {
                return app()->make(ProfileController::class)->edit(request());
            }
        })->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])
            ->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])
            ->name('destroy');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
        Route::delete('/', [NotificationController::class, 'deleteAll'])->name('delete-all');
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('unread');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'manageUsers'])
                ->name('manage');
            Route::get('/{id}/edit', [AdminController::class, 'editUser'])
                ->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateUser'])
                ->name('update');
            Route::delete('/{id}', [AdminController::class, 'deleteUser'])
                ->name('delete');
        });

        // Job Management
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/pending', [AdminController::class, 'pendingJobs'])
                ->name('pending');
            Route::put('/{id}/approve', [AdminController::class, 'approveJob'])
                ->name('approve');
            Route::put('/{id}/reject', [AdminController::class, 'rejectJob'])
                ->name('reject');
        });
    });

    // Candidate Routes
    Route::prefix('candidate')->name('candidate.')->middleware('role:candidate')->group(function () {
        // Dashboard
        Route::get('/dashboard', [CandidateController::class, 'dashboard'])
            ->name('dashboard');

        // Profile Management
        Route::get('/profile', [CandidateController::class, 'profile'])
            ->name('profile');
        Route::patch('/profile', [CandidateController::class, 'updateProfile'])
            ->name('profile.update');

        // Job Applications
        Route::prefix('job-applications')->name('job-applications.')->group(function () {
            Route::get('/', [CandidateController::class, 'jobApplications'])
                ->name('index');
            Route::delete('/{applicationId}', [JobApplicationController::class, 'cancel'])
                ->name('cancel');
        });
    });

    // Job Applications (for candidates)
    Route::post('/job-listings/{jobId}/apply', [JobApplicationController::class, 'apply'])
        ->middleware('role:candidate')
        ->name('job-listings.apply');

    // Employer Routes
    Route::prefix('employer')->name('employer.')->middleware('role:employer')->group(function () {
        // Dashboard
        Route::get('/dashboard', [EmployerController::class, 'dashboard'])
            ->name('dashboard');

        // Profile Management
        Route::get('/profile', [EmployerController::class, 'profile'])
            ->name('profile');
        Route::patch('/profile', [EmployerController::class, 'updateProfile'])
            ->name('profile.update');

        // Job Applications Management
        Route::get('/job/{jobId}/applications', [JobApplicationController::class, 'viewApplications'])
            ->name('job-applications.view');
        Route::put('/application/{applicationId}/status', [JobApplicationController::class, 'updateStatus'])
            ->name('application.update-status');
    });

    // New route for adding a job post
    Route::get('/add_post', [JobListingController::class, 'create'])
        ->middleware('role:employer')
        ->name('job-listings.create');

    // Job Listings Management (for employers)
    Route::prefix('job-listings')->name('job-listings.')->middleware('role:employer')->group(function () {
        Route::post('/', [JobListingController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [JobListingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [JobListingController::class, 'update'])->name('update');
        Route::delete('/{id}', [JobListingController::class, 'destroy'])->name('destroy');
    });

    // Chat Routes
    Route::prefix('chat')->name('chat.')->middleware('auth')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');

        // Debug routes - only in local environment
        if (app()->environment('local')) {
            Route::get('/debug', function () {
                $user = Auth::user();
                $conversations = Conversation::where(function($query) use ($user) {
                    $query->where('employer_id', $user->id)
                          ->orWhere('candidate_id', $user->id);
                })->with(['employer', 'candidate', 'messages'])->get();

                return view('chat.debug', [
                    'user' => $user,
                    'conversations' => $conversations
                ]);
            })->name('debug');

            Route::get('/debug/users', function () {
                $user = Auth::user();
                $role = $user->role === 'employer' ? 'candidate' : 'employer';
                $users = \App\Models\User::where('role', $role)->get(['id', 'name', 'email', 'role']);

                return response()->json([
                    'current_user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'target_role' => $role,
                    'users' => $users
                ]);
            })->name('debug.users');
        }
    });
});

// Include Auth Routes
require __DIR__.'/auth.php';

// Test image display
Route::get('/test-image', function() {
    return view('test-image');
});
