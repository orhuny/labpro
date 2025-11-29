@extends('layouts.app')

@section('title', __('common.add_user'))
@section('page-title', __('common.add_user'))

@section('content')
<div class="px-8 py-6">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="max-w-2xl mx-auto space-y-6">
                    <div class="form-group">
                        <label for="name" class="form-label">{{ __('common.name') }} *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-input">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">{{ __('common.email') }} *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-input">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">{{ __('common.role') }} *</label>
                        <select name="role" id="role" required class="form-select">
                            <option value="">{{ __('common.select_role') }}</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('common.admin') }}</option>
                            <option value="lab_technician" {{ old('role') == 'lab_technician' ? 'selected' : '' }}>{{ __('common.lab_technician') }}</option>
                            <option value="receptionist" {{ old('role') == 'receptionist' ? 'selected' : '' }}>{{ __('common.receptionist') }}</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">{{ __('common.password') }} *</label>
                        <input type="password" name="password" id="password" required class="form-input">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">{{ __('common.confirm_password') }} *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="form-input">
                    </div>

                    <div class="flex justify-center gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('users.index') }}" class="btn-secondary">
                            {{ __('common.cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            {{ __('common.create') }} {{ __('common.users') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

