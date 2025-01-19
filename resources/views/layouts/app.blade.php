<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Campaign Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/css/animations.css', 'resources/css/theme.css', 'resources/css/colors.css', 'resources/css/components.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-secondary-50 font-['Inter'] antialiased min-h-screen flex flex-col">
    <nav class="bg-white shadow-lg bg-opacity-95 backdrop-filter backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                        <h1 class="text-xl font-bold text-primary-600">SMS Manager</h1>
                    </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('groups.index') }}" class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}">Groups</a>
                        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a>
                        <a href="{{ route('campaigns.index') }}" class="nav-link {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">Campaigns</a>
                        <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a>
                        <a href="{{ route('templates.index') }}" class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}">Templates</a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="ml-3 relative">
                        <div>
                            <button type="button" class="flex items-center text-sm text-secondary-700 hover:text-secondary-800 focus:outline-none" id="user-menu" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full" src="" alt="User Avatar">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
    <div class="alert-success px-4 py-3 rounded relative mb-4 max-w-7xl mx-auto mt-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
            <svg class="fill-current h-6 w-6 text-primary-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </button>
    </div>
@endif    
    <main class="flex-grow">
        <div class="fade-in">
        @yield('content')
    </div>
    </main>
    
    <footer class="bg-white border-t border-secondary-200 py-4">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center text-sm text-secondary-500">
                SMS Campaign Manager &copy; {{ date('Y') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
