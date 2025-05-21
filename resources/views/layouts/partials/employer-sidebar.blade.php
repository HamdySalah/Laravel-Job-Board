<li class="sidebar-menu-item">
    <a href="{{ route('employer.dashboard') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-tachometer-alt"></i></span>
        <span>Dashboard</span>
    </a>
</li>

<li class="sidebar-menu-header">Job Management</li>
<li class="sidebar-menu-item">
    <a href="{{ route('job-listings.create') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-plus-circle"></i></span>
        <span>Post a New Job</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('employer.jobs.active') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-briefcase"></i></span>
        <span>Active Jobs</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('employer.jobs.pending') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-clock"></i></span>
        <span>Pending Jobs</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('employer.jobs.expired') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-calendar-times"></i></span>
        <span>Expired Jobs</span>
    </a>
</li>

<li class="sidebar-menu-header">Applications</li>
<li class="sidebar-menu-item">
    <a href="{{ route('employer.applications.all') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-file-alt"></i></span>
        <span>All Applications</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('employer.applications.new') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-bell"></i></span>
        <span>New Applications</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('employer.applications.shortlisted') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-star"></i></span>
        <span>Shortlisted</span>
    </a>
</li>

<li class="sidebar-menu-header">Company</li>
<li class="sidebar-menu-item">
    <a href="{{ route('employer.profile') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-building"></i></span>
        <span>Company Profile</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('profile.edit') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-user-cog"></i></span>
        <span>My Account</span>
    </a>
</li>
