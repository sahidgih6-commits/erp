<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ইআরপি সিস্টেম')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @php
        $shopSetting = null;
        if (auth()->check()) {
            if (auth()->user()->isOwner()) {
                $shopSetting = auth()->user()->shopSetting;
            } elseif (auth()->user()->isManager() && auth()->user()->creator) {
                $shopSetting = auth()->user()->creator->shopSetting;
            } elseif (auth()->user()->isSalesman() && auth()->user()->creator && auth()->user()->creator->creator) {
                $shopSetting = auth()->user()->creator->creator->shopSetting;
            }
        }
    @endphp
    
    @if($shopSetting)
    <style>
        :root {
            --primary-color: {{ $shopSetting->primary_color }};
            --secondary-color: {{ $shopSetting->secondary_color }};
            --accent-color: {{ $shopSetting->accent_color }};
            --text-color: {{ $shopSetting->text_color }};
        }
        
        body {
            font-family: {{ $shopSetting->font_family }}, sans-serif;
        }
        
        /* Apply custom colors */
        .bg-blue-500, .bg-blue-600 { background-color: var(--primary-color) !important; }
        .bg-green-500, .bg-green-600 { background-color: var(--secondary-color) !important; }
        .bg-orange-500, .bg-orange-600, .bg-yellow-500 { background-color: var(--accent-color) !important; }
        .text-blue-600 { color: var(--primary-color) !important; }
        .text-green-600 { color: var(--secondary-color) !important; }
        .border-blue-500 { border-color: var(--primary-color) !important; }
        .border-green-500 { border-color: var(--secondary-color) !important; }
        
        {{ $shopSetting->custom_css }}
    </style>
    @endif
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    @auth
                        @php
                            $businessName = 'ইআরপি সিস্টেম';
                            $dashboardRoute = '#';
                            
                            if (auth()->user()->isSuperAdmin()) {
                                $dashboardRoute = route('superadmin.dashboard');
                            } elseif (auth()->user()->isOwner()) {
                                $dashboardRoute = route('owner.dashboard');
                                if (auth()->user()->business_id) {
                                    $business = \App\Models\Business::find(auth()->user()->business_id);
                                    if ($business) {
                                        $businessName = $business->name;
                                    }
                                }
                            } elseif (auth()->user()->isManager() || auth()->user()->isSalesman()) {
                                $dashboardRoute = route('manager.dashboard');
                                if (auth()->user()->business_id) {
                                    $business = \App\Models\Business::find(auth()->user()->business_id);
                                    if ($business) {
                                        $businessName = $business->name;
                                    }
                                }
                            }
                        @endphp
                        
                        <a href="{{ $dashboardRoute }}" class="text-xl font-bold text-gray-800 hover:text-blue-600 transition-colors">
                            {{ $businessName }}
                        </a>
                    @else
                        <span class="text-xl font-bold text-gray-800">ইআরপি সিস্টেম</span>
                    @endauth
                    @auth
                        <span class="ml-4 text-sm text-gray-600">
                            @if(auth()->user()->isSuperAdmin())
                                সুপার অ্যাডমিন
                            @elseif(auth()->user()->isOwner())
                                মালিক
                            @elseif(auth()->user()->isManager())
                                ম্যানেজার
                            @else
                                সেলসম্যান
                            @endif
                        </span>
                    @endauth
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Language Switcher -->
                        <div class="relative">
                            <select onchange="window.location.href=this.value" class="bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="{{ route('locale', 'bn') }}" {{ session('locale', 'bn') == 'bn' ? 'selected' : '' }}>🇧🇩 বাংলা</option>
                                <option value="{{ route('locale', 'en') }}" {{ session('locale', 'bn') == 'en' ? 'selected' : '' }}>🇬🇧 English</option>
                            </select>
                        </div>
                        
                        <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                লগআউট
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="py-6">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
