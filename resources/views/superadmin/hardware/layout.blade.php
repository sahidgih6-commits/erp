<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ __('pos.hardware_management') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.documentElement.lang = "{{ app()->getLocale() }}";
    </script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('pos.hardware_management') }}</h1>
                    <div class="flex items-center gap-4">
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
</body>
</html>
