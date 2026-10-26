<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Community Recovery System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    {{-- Sidebar Overlay for Mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h5>
                <span class="brand-icon-box"><i class="bi bi-shield-check"></i></span>
                Community Recovery
            </h5>
            <small>Management System</small>
        </div>

        <div class="py-3">
            <div class="nav-section">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section mt-3">Reports</div>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') && !request()->has('type') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> All Reports
            </a>
            <a href="{{ route('admin.reports.index', ['type' => 'lost_property']) }}" class="nav-link {{ request()->fullUrlIs('*type=lost_property*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> Lost Property
            </a>
            <a href="{{ route('admin.reports.index', ['type' => 'found_property']) }}" class="nav-link {{ request()->fullUrlIs('*type=found_property*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Found Property
            </a>
            <a href="{{ route('admin.reports.index', ['type' => 'missing_pet']) }}" class="nav-link {{ request()->fullUrlIs('*type=missing_pet*') ? 'active' : '' }}">
                <i class="bi bi-search-heart"></i> Missing Pets
            </a>
            <a href="{{ route('admin.reports.index', ['type' => 'found_pet']) }}" class="nav-link {{ request()->fullUrlIs('*type=found_pet*') ? 'active' : '' }}">
                <i class="bi bi-heart"></i> Found Pets
            </a>
            <a href="{{ route('admin.reports.pending') }}" class="nav-link {{ request()->routeIs('admin.reports.pending') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> Moderation Queue
            </a>

            <div class="nav-section mt-3">Recovery</div>
            <a href="#" class="nav-link">
                <i class="bi bi-link-45deg"></i> Possible Matches
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark-check"></i> Recovery Claims
            </a>

            <div class="nav-section mt-3">Community</div>
            <a href="#" class="nav-link">
                <i class="bi bi-map"></i> Community Map
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-megaphone"></i> Announcements
            </a>

            <div class="nav-section mt-3">Administration</div>
            <a href="#" class="nav-link">
                <i class="bi bi-people"></i> User Management
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-bar-chart-line"></i> Analytics
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-clock-history"></i> Audit Logs
            </a>

            @if(auth()->user()->isSuperAdmin())
            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i> System Settings
            </a>
            @endif
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Top Navbar --}}
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark d-lg-none me-2 p-0" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 fw-semibold text-truncate" style="max-width: 380px;">
                    @hasSection('page-title')
                        @yield('page-title')
                    @else
                        @yield('title', 'Dashboard')
                    @endif
                </h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                {{-- Notifications --}}
                <div class="position-relative">
                    <a href="#" class="text-dark text-decoration-none">
                        <i class="bi bi-bell fs-5"></i>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                        <span class="badge bg-danger rounded-pill notification-badge">
                            {{ auth()->user()->unreadNotifications()->count() }}
                        </span>
                        @endif
                    </a>
                </div>

                {{-- Profile Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-link text-dark text-decoration-none dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                             style="width:32px;height:32px;">
                            <span class="text-white fw-bold" style="font-size:0.75rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        </div>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->role->name }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Content Area --}}
        <div class="content-area">
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
