<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Lab Management System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    @if(auth()->check())
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="top-nav">
            <div class="max-w-full mx-auto px-6">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center shadow-lg">
                            <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="width: 20px; height: 20px; flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">
                            LabPro
                        </span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('dashboard') }}" 
                           class="nav-top-link {{ request()->routeIs('dashboard') ? 'nav-top-link-active' : '' }}">
                            {{ __('common.dashboard') }}
                        </a>
                        <a href="{{ route('patients.index') }}" 
                           class="nav-top-link {{ request()->routeIs('patients.*') ? 'nav-top-link-active' : '' }}">
                            {{ __('common.patients') }}
                        </a>
                        <a href="{{ route('test-results.index') }}" 
                           class="nav-top-link {{ request()->routeIs('test-results.*') ? 'nav-top-link-active' : '' }}">
                            {{ __('common.test_results') }}
                        </a>
                        <a href="{{ route('tests.index') }}" 
                           class="nav-top-link {{ request()->routeIs('tests.*') ? 'nav-top-link-active' : '' }}">
                            {{ __('common.tests') }}
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('test-categories.index') }}" 
                           class="nav-top-link {{ request()->routeIs('test-categories.*') ? 'nav-top-link-active' : '' }}">
                            {{ __('common.categories') }}
                        </a>
                        <a href="{{ route('users.index') }}" 
                           class="nav-top-link {{ request()->routeIs('users.*') ? 'nav-top-link-active' : '' }}">
                            {{ __('common.users') }}
                        </a>
                        @endif
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Language Switcher -->
                        <div class="relative">
                            <select onchange="window.location.href='{{ route('language.switch', '') }}/' + this.value" class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="tr" {{ app()->getLocale() == 'tr' ? 'selected' : '' }}>🇹🇷 TR</option>
                                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>🇬🇧 EN</option>
                            </select>
                        </div>
                        
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>

                        <!-- User Profile -->
                        <div class="flex items-center space-x-3 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-md">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-blue-600 uppercase tracking-wide">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                            </div>
                        </div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="{{ __('common.logout') }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="content-container">
            <!-- Flash Messages -->
            <div class="mb-6">
                @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <div class="flex items-center">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 20px; height: 20px; flex-shrink: 0;" class="mr-3">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <div class="flex items-center">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 20px; height: 20px; flex-shrink: 0;" class="mr-3">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-bold">{{ session('error') }}</span>
                    </div>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <div class="flex items-start">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 20px; height: 20px; flex-shrink: 0;" class="mr-3 mt-0.5">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-semibold mb-1">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @yield('content')
        </main>
    </div>

    <script>
        // Update current date if needed
        if (document.getElementById('current-date')) {
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }
    </script>
    @else
    <!-- Login/Register Pages (No Navigation) -->
    <div class="min-h-screen">
        @yield('content')
    </div>
    @endif
</body>
</html>
