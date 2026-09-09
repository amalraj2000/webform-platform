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
        
        <style>
            .left-panel {
                background: linear-gradient(135deg, #0d6efd, #0056b3);
            }
            .left-panel-content {
                max-width: 480px;
            }
        </style>
    </head>
    <body class="bg-light">
        <div class="row g-0 min-vh-100">
            <!-- Left Side: Branding / Marketing -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center text-white p-5 left-panel">
                <div class="text-center left-panel-content">
                    <h1 class="display-4 fw-bold mb-4">Grow Your Business</h1>
                    <p class="lead mb-5 opacity-75">Join thousands of users sending smart, automated email campaigns with Mailercloud.</p>
                    <svg width="240" height="240" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                        <path d="M12 12v9"></path>
                        <path d="m8 17 4 4 4-4"></path>
                    </svg>
                </div>
            </div>

            <!-- Right Side: Auth Form -->
            <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center p-5 bg-white">
                <div class="w-100" style="max-width: 450px;">
                    <div class="text-center mb-5">
                        <a href="/" class="text-decoration-none">
                            <img src="{{ asset('images/mailercloud-logo.svg') }}" alt="Mailercloud Logo" height="40" class="mb-3">
                        </a>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
