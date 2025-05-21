<li class="sidebar-menu-item">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-tachometer-alt"></i></span>
        <span>Dashboard</span>
    </a>
</li>

<li class="sidebar-menu-header">Job Management</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.jobs.pending') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-clock"></i></span>
        <span>Pending Jobs</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.jobs.approved') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-check-circle"></i></span>
        <span>Approved Jobs</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.jobs.rejected') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-times-circle"></i></span>
        <span>Rejected Jobs</span>
    </a>
</li>

<li class="sidebar-menu-header">User Management</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.users.employers') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-building"></i></span>
        <span>Employers</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.users.candidates') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-user-tie"></i></span>
        <span>Candidates</span>
    </a>
</li>

<li class="sidebar-menu-header">Reports</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.reports.jobs') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-chart-bar"></i></span>
        <span>Job Statistics</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.reports.applications') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-chart-line"></i></span>
        <span>Application Statistics</span>
    </a>
</li>

<li class="sidebar-menu-header">Settings</li>
<li class="sidebar-menu-item">
    <a href="{{ route('admin.settings') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-cog"></i></span>
        <span>Site Settings</span>
    </a>
</li>
<li class="sidebar-menu-item">
    <a href="{{ route('profile.edit') }}" class="sidebar-menu-link">
        <span class="sidebar-menu-icon"><i class="fas fa-user-cog"></i></span>
        <span>My Profile</span>
    </a>
</li>
