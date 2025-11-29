@extends('layouts.app')

@section('title', __('common.patients'))
@section('page-title', __('common.patients'))

@section('content')
<div class="px-8 py-6">
    <!-- Page Header with Action -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('common.all_patients') }}</h2>
            <p class="text-gray-600 text-sm mt-1">{{ __('common.manage_patients') }}</p>
        </div>
        <a href="{{ route('patients.create') }}" class="btn-primary">
            + {{ __('common.add_patient') }}
        </a>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <!-- Search Form -->
            <form method="GET" action="{{ route('patients.index') }}" class="mb-6">
                <div class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="{{ __('common.search') }}..." 
                           class="form-input flex-1">
                    <button type="submit" class="btn-primary whitespace-nowrap">
                        {{ __('common.search') }}
                    </button>
                    @if(request('search'))
                    <a href="{{ route('patients.index') }}" class="btn-secondary whitespace-nowrap">
                        {{ __('common.clear') }}
                    </a>
                    @endif
                </div>
            </form>

            <!-- Patients Table -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.patient_id') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.age') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.gender') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.phone') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.email') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                    <tbody>
                            @forelse($patients as $patient)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $patient->patient_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $patient->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->age ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($patient->gender ?? 'N/A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->phone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->email ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                    <a href="{{ route('patients.show', $patient) }}" class="text-emerald-600 hover:text-emerald-800 font-semibold hover:underline">{{ __('common.view') }}</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('patients.edit', $patient) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold hover:underline">{{ __('common.edit') }}</a>
                                    @if(auth()->user()->isAdmin())
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold hover:underline">{{ __('common.delete') }}</button>
                                    </form>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('common.no_patients') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

