<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mailercloud') }}</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('images/mailerclound-logo-2.jpeg') }}">
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <style>
            .sidebar {
                width: 260px;
                background-color: #ffffff;
                border-right: 1px solid #dee2e6;
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                overflow-y: auto;
                z-index: 1000;
            }
            .main-content {
                margin-left: 260px;
                min-height: 100vh;
                background-color: #f8f9fa;
            }
            .sidebar-link {
                color: #495057;
                text-decoration: none;
                padding: 10px 16px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                margin-bottom: 5px;
                font-weight: 500;
            }
            .sidebar-link:hover {
                background-color: #e9ecef;
                color: #212529;
            }
            .sidebar-link.active {
                background-color: #0d6efd;
                color: #ffffff;
            }
            .profile-circle {
                width: 40px;
                height: 40px;
                background-color: #e0e7ff;
                color: #3730a3;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 1.2rem;
            }
        </style>
    </head>
    <body class="text-dark">
        
        <!-- Sidebar -->
        <aside class="sidebar p-3 d-flex flex-column">
            <!-- Brand -->
            <a href="/dashboard" class="d-flex align-items-center justify-content-center mb-4 text-decoration-none">
                <img src="{{ asset('images/mailercloud-logo.svg') }}" alt="Mailercloud" height="32">
            </a>

            <!-- Navigation Links -->
            <nav class="flex-grow-1">
                @if(Auth::user()->isSuperAdmin())
                    <!-- Super Admin Menu -->
                    <div class="small text-muted fw-bold text-uppercase mb-2 mt-4" style="letter-spacing: 0.5px; font-size: 0.75rem;">Platform</div>
                    <a href="/superadmin/dashboard" class="sidebar-link {{ request()->is('superadmin/dashboard') ? 'active' : '' }}">
                        <svg class="me-2" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="/superadmin/accounts" class="sidebar-link {{ request()->is('superadmin/accounts*') || request()->is('superadmin/forms*') ? 'active' : '' }}">
                        <svg class="me-2" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Companies
                    </a>
                @else
                    <!-- Company Admin Menu -->
                    <div class="small text-muted fw-bold text-uppercase mb-2 mt-4" style="letter-spacing: 0.5px; font-size: 0.75rem;">Main Menu</div>
                    <a href="/admin/dashboard" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <svg class="me-2" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    
                    <div class="small text-muted fw-bold text-uppercase mb-2 mt-4" style="letter-spacing: 0.5px; font-size: 0.75rem;">Management</div>
                    <a href="/admin/forms" class="sidebar-link {{ request()->is('admin/forms*') ? 'active' : '' }}">
                        <svg class="me-2" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Forms
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-content d-flex flex-column">
            
            <!-- Top Header -->
            <header class="bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center sticky-top">
                <div class="fw-bold fs-5 flex-grow-1 me-4">
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <form method="POST" action="{{ route('logout') }}" class="m-0" id="logout-form">
                        @csrf
                        <button type="button" onclick="confirmLogout()" class="btn btn-sm btn-outline-secondary">Log Out</button>
                    </form>
                    <div class="profile-circle" title="{{ Auth::user()->name }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-grow-1 p-4">
                {{ $slot }}
            </main>
        </div>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            function confirmLogout() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You will be logged out of your session.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, log out!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            }
        </script>
    </body>
</html>
