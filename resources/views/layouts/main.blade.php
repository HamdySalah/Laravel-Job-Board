<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Job Board') }} - @yield('title', 'Find Your Dream Job')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer-custom.css') }}">

    @stack('styles')
</head>
<body>
    <header class="sticky-top">
        <div class="top-bar py-2 bg-gradient-primary text-white d-none d-md-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item me-3"><i class="fas fa-phone-alt me-1"></i> (123) 456-7890</li>
                            <li class="list-inline-item"><i class="fas fa-envelope me-1"></i> info@jobboard.com</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-end">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item me-3"><a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="list-inline-item me-3"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
                            <li class="list-inline-item me-3"><a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a></li>
                            <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="https://cdn-icons-png.flaticon.com/512/2936/2936630.png" alt="JobBoard Logo" width="40" height="40" class="me-2">
                    <span class="fw-bold text-primary fs-4">Job</span><span class="fw-bold fs-4">Board</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="fas fa-home me-1"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('job-listings*') ? 'active' : '' }}" href="{{ route('job-listings.index') }}">
                                <i class="fas fa-briefcase me-1"></i> Jobs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('employers*') ? 'active' : '' }}" href="{{ url('/employers') }}">
                                <i class="fas fa-building me-1"></i> Employers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('about*') ? 'active' : '' }}" href="{{ url('/about') }}">
                                <i class="fas fa-info-circle me-1"></i> About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact*') ? 'active' : '' }}" href="{{ url('/contact') }}">
                                <i class="fas fa-envelope me-1"></i> Contact
                            </a>
                        </li>
                    </ul>

                    <div class="d-flex">
                        @auth
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2" aria-labelledby="userDropdown">
                                    @if(Auth::user()->role === 'admin')
                                        <li><a class="dropdown-item py-2 px-3" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard</a></li>
                                    @elseif(Auth::user()->role === 'employer')
                                        <li><a class="dropdown-item py-2 px-3" href="{{ route('employer.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Employer Dashboard</a></li>
                                        <li><a class="dropdown-item py-2 px-3" href="{{ url('/add_post') }}"><i class="fas fa-plus-circle me-2"></i> Post a Job</a></li>
                                    @elseif(Auth::user()->role === 'candidate')
                                        <li><a class="dropdown-item py-2 px-3" href="{{ route('candidate.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Candidate Dashboard</a></li>
                                        <li><a class="dropdown-item py-2 px-3" href="{{ route('candidate.job-applications.index') }}"><i class="fas fa-file-alt me-2"></i> My Applications</a></li>
                                    @endif
                                    <li><a class="dropdown-item py-2 px-3" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit me-2"></i> Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 px-3 text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2 rounded-pill px-4"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                            <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4"><i class="fas fa-user-plus me-1"></i> Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="py-4">
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 animate__animated animate__fadeIn" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 animate__animated animate__fadeIn" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gradient-custom text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-brand mb-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/2936/2936630.png" alt="JobBoard Logo" width="50" height="50" class="mb-3">
                        <h5 class="mb-3 fw-bold d-flex align-items-center">
                            <span class="text-white me-2">Job</span><span class="text-white opacity-75">Board</span>
                        </h5>
                    </div>
                    <p>Find your dream job or the perfect candidate with our comprehensive job board platform. We connect talented professionals with top employers worldwide.</p>
                    <div class="social-icons mt-4">
                        <a href="#" class="social-icon me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3 fw-bold">Quick Links</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="{{ url('/') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Home</a></li>
                        <li class="mb-2"><a href="{{ route('job-listings.index') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Jobs</a></li>
                        <li class="mb-2"><a href="{{ url('/employers') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Employers</a></li>
                        <li class="mb-2"><a href="{{ url('/about') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> About</a></li>
                        <li><a href="{{ url('/contact') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3 fw-bold">For Employers</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="{{ route('register') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Register</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Login</a></li>
                        <li class="mb-2"><a href="{{ url('/add_post') }}" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Post a Job</a></li>
                        <li><a href="#" class="footer-link"><i class="fas fa-chevron-right me-1"></i> Pricing</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="mb-3 fw-bold">Contact Us</h5>
                    <div class="footer-contact-info">
                        <div class="d-flex mb-3">
                            <div class="icon-box me-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-white">Our Location</h6>
                                <p class="mb-0 text-white-50">123 Job Street, Employment City, CA 94107</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="icon-box me-3">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-white">Phone Number</h6>
                                <p class="mb-0 text-white-50">(123) 456-7890</p>
                            </div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="icon-box me-3">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-white">Email Address</h6>
                                <p class="mb-0 text-white-50">info@jobboard.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="newsletter mt-4">
                        <h6 class="mb-3 text-white">Subscribe to our newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email address">
                            <button class="btn btn-primary" type="button"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom mt-5">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <p class="mb-0">&copy; {{ date('Y') }} Job Board. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="footer-link me-3">Privacy Policy</a>
                        <a href="#" class="footer-link me-3">Terms of Service</a>
                        <a href="#" class="footer-link">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to top button -->
    <button type="button" class="btn btn-primary btn-floating btn-back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Floating action button for job posting -->
    <div class="floating-action-btn">
        <a href="{{ url('/add_post') }}" class="btn btn-primary btn-lg rounded-circle shadow-lg" data-bs-toggle="tooltip" data-bs-placement="left" title="Post a Job">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>
