<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finate - @yield('title', 'Finate')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f8f9fa; color: #343a40; }
        .header { background: #343a40; padding: 15px 0; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .header .navbar-brand .application-logo { height: 45px; }
        .header .nav-link { color: #fff !important; margin-left: 20px; font-weight: 500; transition: color 0.3s; }
        .header .nav-link:hover { color: #28a745 !important; }
        .header .btn-registration { background: #28a745; color: #fff; border: none; padding: 8px 20px; border-radius: 5px; transition: background 0.3s; }
        .header .btn-registration:hover { background: #218838; }
        .card-job, .card-candidate { background: #fff; border: 1px solid #dee2e6; border-radius: 10px; padding: 20px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); transition: transform 0.2s; }
        .card-job:hover, .card-candidate:hover { transform: translateY(-5px); }
        .card-job img, .card-candidate img { border-radius: 50%; width: 120px; height: 120px; object-fit: cover; margin-bottom: 15px; }
        .btn-green { background: #28a745; color: #fff; border: none; padding: 8px 20px; border-radius: 5px; transition: background 0.3s; }
        .btn-green:hover { background: #218838; }
        .btn-outline-secondary { border-color: #6c757d; color: #6c757d; padding: 8px 20px; border-radius: 5px; transition: all 0.3s; }
        .btn-outline-secondary:hover { background: #6c757d; color: #fff; }
        .sidebar { background: #e9ecef; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .footer { background: #343a40; color: #fff; padding: 30px 0; margin-top: 40px; }
        .footer .nav-link { color: #fff; margin: 0 15px; font-weight: 500; }
        .footer .nav-link:hover { color: #28a745; }
        .subscribe-area { background: #28a745; color: #fff; padding: 30px 0; text-align: center; }
        .subscribe-area input { max-width: 350px; margin-right: 10px; display: inline-block; vertical-align: middle; }
        .subscribe-area .btn-dark { vertical-align: middle; }
        @media (max-width: 768px) { .header .nav-link { margin-left: 10px; } .subscribe-area input { max-width: 100%; margin-bottom: 10px; } .card-job img, .card-candidate img { width: 100px; height: 100px; } }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg container">
            <div class="navbar-brand">
                <a href="{{ route('home') }}">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('jobs.index') }}">Find Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('employers.details') }}">Employers</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="candidatesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Candidates
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="candidatesDropdown">
                            <li><a class="dropdown-item" href="{{ route('candidates.index') }}">Candidate</a></li>
                            <li><a class="dropdown-item" href="{{ route('candidates.details') }}">Candidate Details</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('blog') }}">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('pages') }}">Pages</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                </ul>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-registration">+ Registration</a>
                    <a href="{{ route('login') }}" class="btn btn-registration">Login</a>
                @endguest
                @auth
                    <div class="sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="btn btn-registration inline-flex items-center">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    <main class="container my-5">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="subscribe-area">
            <h4>Subscribe for everyday job newsletter.</h4>
            <input type="email" class="form-control d-inline-block" placeholder="Enter your email">
            <button class="btn btn-dark">Subscribe Now</button>
        </div>
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-3 text-center text-md-start">
                    <div class="mb-3"><img src="{{ asset('images/logo.png') }}" alt="Finate Logo"></div>
                    <p>The necessary ecommerce platform that optimi your store popularized the release</p>
                    <div class="d-flex justify-content-center justify-content-md-start mb-3">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h4>Company</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="{{ route('about.us') }}">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Why Exobot</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact With Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Our Partners</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h4>Resources</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="#">Quick Links</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Job Packages</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Post New Job</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('jobs.index') }}">Jobs Listing</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h4>Legal</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="#">Affiliate</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('blog') }}">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Help & Support</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h4>Products</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="#">Start a Trial</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">How It Works</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Features</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Price & Planing</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center mt-3">
                    <p>© 2025 Laravel Job Seeking. Made with ♥ by h,o,m,m,f</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
</body>
</html>