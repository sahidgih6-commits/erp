<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ __('pos.title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.documentElement.lang = "{{ app()->getLocale() }}";
        @if(app()->getLocale() === 'bn')
            document.documentElement.setAttribute('dir', 'auto');
        @endif
    </script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('pos.title') }}</h1>
                        <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
                            {{ auth()->user()->business->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Back to Main Dashboard Button -->
                        @if(auth()->user()->hasRole('owner'))
                            <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                {{ __('pos.main_dashboard') }}
                            </a>
                        @endif
                        
                        <!-- Language Switcher -->
                        <div class="flex gap-2">
                            <a href="{{ route('locale', 'en') }}" 
                               class="px-3 py-1 rounded text-sm {{ app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                                English
                            </a>
                            <a href="{{ route('locale', 'bn') }}" 
                               class="px-3 py-1 rounded text-sm {{ app()->getLocale() === 'bn' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                                বাংলা
                            </a>
                        </div>
                        <!-- User Menu -->
                        <div class="relative">
                            <button onclick="toggleUserMenu()" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                                <span class="text-sm">{{ auth()->user()->name }}</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50">
                                <a href="#" class="block px-4 py-2 hover:bg-gray-100">{{ __('pos.profile') }}</a>
                                <a href="#" class="block px-4 py-2 hover:bg-gray-100">{{ __('pos.settings') }}</a>
                                <a href="{{ route('logout') }}" class="block px-4 py-2 hover:bg-gray-100">{{ __('pos.logout') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            const menu = document.getElementById('userMenu');
            const button = event.target.closest('button');
            if (!button && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
