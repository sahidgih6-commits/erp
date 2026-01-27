@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('pos.add_new_user') }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('pos.create_manager_salesman_cashier') }}</p>
                </div>
                <a href="{{ route('owner.users.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('owner.users.store') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('pos.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('pos.phone') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone') }}"
                        placeholder="01XXXXXXXXX"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                        required
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('pos.password') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                        required
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">{{ __('pos.min_8_characters') }}</p>
                </div>

                <!-- Role Selection -->
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('pos.role') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <!-- Manager -->
                        <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input 
                                type="radio" 
                                name="role" 
                                value="manager" 
                                class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                {{ old('role') === 'manager' ? 'checked' : '' }}
                                required
                            >
                            <div>
                                <div class="font-semibold text-gray-900">{{ __('pos.manager') }}</div>
                                <p class="text-sm text-gray-600">{{ __('pos.manager_description') }}</p>
                            </div>
                        </label>

                        <!-- Salesman -->
                        <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input 
                                type="radio" 
                                name="role" 
                                value="salesman" 
                                class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                {{ old('role') === 'salesman' ? 'checked' : '' }}
                            >
                            <div>
                                <div class="font-semibold text-gray-900">{{ __('pos.salesman') }}</div>
                                <p class="text-sm text-gray-600">{{ __('pos.salesman_description') }}</p>
                            </div>
                        </label>

                        <!-- Cashier -->
                        <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 {{ !$posEnabled ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input 
                                type="radio" 
                                name="role" 
                                value="cashier" 
                                class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                {{ old('role') === 'cashier' ? 'checked' : '' }}
                                {{ !$posEnabled ? 'disabled' : '' }}
                            >
                            <div>
                                <div class="font-semibold text-gray-900">
                                    {{ __('pos.cashier') }}
                                    @if(!$posEnabled)
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">{{ __('pos.pos_required') }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">{{ __('pos.cashier_description') }}</p>
                                @if(!$posEnabled)
                                    <p class="text-xs text-yellow-700 mt-1">⚠️ {{ __('pos.contact_superadmin_pos') }}</p>
                                @endif
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('owner.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        {{ __('pos.cancel') }}
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        {{ __('pos.create_user') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
