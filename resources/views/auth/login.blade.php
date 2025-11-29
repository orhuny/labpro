@extends('layouts.app')

@section('title', __('common.login'))

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(to bottom right, #ecfdf5, #d1fae5, #a7f3d0);">
    <div class="max-w-md w-full">
        <div class="card">
            <div class="card-header text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; flex-shrink: 0;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">
                    {{ __('common.laboratory_management_system') }}
                </h2>
                <p class="text-blue-100 text-sm font-medium">{{ __('common.sign_in_to_account') }}</p>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-error mb-6">
                    <div class="flex items-center">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 20px; height: 20px; flex-shrink: 0;" class="mr-2">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-semibold">{{ $errors->first() }}</span>
                    </div>
                </div>
                @endif

                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="email" class="form-label">{{ __('common.email') }}</label>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   class="form-input" 
                                   placeholder="{{ __('common.email') }}" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">{{ __('common.password') }}</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="form-input" 
                                   placeholder="{{ __('common.password') }}">
                        </div>
                    </div>

                        <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm font-semibold text-gray-700">
                                {{ __('common.remember_me') }}
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary w-full">
                            {{ __('common.sign_in') }}
                        </button>
                    </div>

                    <div class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            {{ __('common.dont_have_account') }} 
                            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-bold">
                                {{ __('common.register_here') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

