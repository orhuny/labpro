@extends('layouts.app')

@section('title', __('common.users'))
@section('page-title', __('common.users'))

@section('content')
<div class="px-8 py-6">
    <!-- Page Header with Action -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('common.all_users') }}</h2>
            <p class="text-gray-600 text-sm mt-1">{{ __('common.manage_users') }}</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">
            + {{ __('common.add_user') }}
        </a>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('users.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="role" class="form-label">{{ __('common.filter_by_role') }}</label>
                        <select name="role" id="role" onchange="this.form.submit()" class="form-select">
                            <option value="">{{ __('common.all_roles') }}</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('common.admin') }}</option>
                            <option value="lab_technician" {{ request('role') == 'lab_technician' ? 'selected' : '' }}>{{ __('common.lab_technician') }}</option>
                            <option value="receptionist" {{ request('role') == 'receptionist' ? 'selected' : '' }}>{{ __('common.receptionist') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="form-label">{{ __('common.search') }}</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="{{ __('common.search') }}..." class="form-input">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            {{ __('common.search') }}
                        </button>
                    </div>
                </div>
                @if(request('role') || request('search'))
                <div class="mt-2">
                    <a href="{{ route('users.index') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('common.clear_filters') }}</a>
                </div>
                @endif
            </form>

            <!-- Users Table -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.role') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.created_at') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($user->role === 'admin') bg-purple-100 text-purple-800
                                    @elseif($user->role === 'lab_technician') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @if($user->role === 'admin')
                                        {{ __('common.admin') }}
                                    @elseif($user->role === 'lab_technician')
                                        {{ __('common.lab_technician') }}
                                    @else
                                        {{ __('common.receptionist') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800 font-semibold mr-3">{{ __('common.view') }}</a>
                                <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold mr-3">{{ __('common.edit') }}</a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">{{ __('common.delete') }}</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('common.no_users') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

