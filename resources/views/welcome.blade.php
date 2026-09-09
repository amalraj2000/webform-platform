<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mailercloud - Dynamic Webforms for Business</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background-color: #ffffff;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="w-full py-6 px-8 flex justify-between items-center absolute top-0 z-10">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span class="text-2xl font-extrabold text-white tracking-tight">Mailercloud</span>
            </div>
            
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="font-semibold text-white hover:text-gray-200 transition">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-white hover:text-gray-200 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-white text-indigo-900 px-5 py-2.5 rounded-full font-bold hover:bg-gray-100 transition shadow-lg shadow-white/20">Create Account</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="flex-grow flex items-center justify-center hero-bg relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 bg-black/40"></div>
            
            <div class="relative z-10 max-w-4xl mx-auto px-6 text-center pt-24 pb-32">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm text-white text-sm font-medium mb-8">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-400"></span>
                    Now accepting High-Volume Submissions
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight mb-8 leading-tight">
                    Build Dynamic Forms for <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">Your Business</span>
                </h1>
                
                <p class="text-xl text-gray-300 mb-12 max-w-2xl mx-auto leading-relaxed">
                    Create an account here to manage your business effectively. Design custom webforms, capture high-throughput responses, and analyze data securely in the cloud.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-indigo-400 hover:to-purple-500 transition-all shadow-xl hover:scale-105 hover:shadow-indigo-500/25 flex items-center justify-center gap-2">
                        Get Started for Free
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                    
                    <a href="{{ route('login') }}" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/20 transition-all flex items-center justify-center">
                        Sign into Dashboard
                    </a>
                </div>
            </div>
        </main>

        <!-- Feature Highlights -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <!-- Feature 1 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">Dynamic Builder</h3>
                        <p class="text-gray-600">Create forms with complex conditional logic, multiple input types, and robust validation on the fly.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 transform -rotate-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">High Concurrency</h3>
                        <p class="text-gray-600">Built for scale. Our asynchronous queue pipeline ensures you never drop a submission, even during massive spikes.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">Multi-Tenant Isolation</h3>
                        <p class="text-gray-600">Enterprise-grade security guarantees your data and your forms are strictly isolated from other businesses.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Mailercloud Forms. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
