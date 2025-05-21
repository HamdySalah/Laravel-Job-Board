<li class="sidebar-menu-item">
    <a href="{{ route('candidate.dashboard') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-tachometer-alt"></i></span>
        <span>Dashboard</span>
    </a>
</li>

<li class="sidebar-menu-header">Job Search</li>
<li class="sidebar-menu-item">
    <a href="{{ route('job-listings.index') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-search"></i></span>
        <span>Find Jobs</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('candidate.jobs.saved') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-bookmark"></i></span>
        <span>Saved Jobs</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('candidate.jobs.recommended') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-thumbs-up"></i></span>
        <span>Recommended Jobs</span>
    </a>
</li>

<li class="sidebar-menu-header">Applications</li>
<li class="sidebar-menu-item">
    <a href="{{ route('candidate.job-applications.index') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-file-alt"></i></span>
        <span>My Applications</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('candidate.applications.active') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-spinner"></i></span>
        <span>Active Applications</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('candidate.applications.archived') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-archive"></i></span>
        <span>Archived Applications</span>
    </a>
</li>

<li class="sidebar-menu-header">Profile</li>
<li class="sidebar-menu-item">
    <a href="{{ route('candidate.profile') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-user"></i></span>
        <span>My Resume</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('profile.edit') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-user-cog"></i></span>
        <span>Account Settings</span>
    </a>
</li>
