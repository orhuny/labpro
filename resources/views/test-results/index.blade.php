@extends('layouts.app')

@section('title', __('common.test_results'))
@section('page-title', __('common.test_results'))

@section('content')
<div class="px-8 py-6">
    <!-- Page Header with Action -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('common.all_test_results') }}</h2>
            <p class="text-gray-600 text-sm mt-1">{{ __('common.view_test_results') }}</p>
        </div>
        <a href="{{ route('test-results.create') }}" class="btn-primary">
            + {{ __('common.order_new_test') }}
        </a>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('test-results.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="form-label">{{ __('common.filter_by_status') }}</label>
                        <select name="status" id="status" onchange="this.form.submit()" class="form-select">
                            <option value="">{{ __('common.all_statuses') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('common.pending') }}</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('common.in_progress') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('common.completed') }}</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('common.cancelled') }}</option>
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
                @if(request('status') || request('search'))
                <div class="mt-4 text-center">
                    <a href="{{ route('test-results.index') }}" class="text-sm text-emerald-600 hover:text-emerald-800 font-semibold">{{ __('common.clear_filters') }}</a>
                </div>
                @endif
            </form>

            <!-- Test Results Table -->
            <div class="table-wrapper">
                <div class="space-y-3">
                    @forelse($groupedResults as $groupKey => $groupResults)
                        @php
                            $firstResult = $groupResults->first();
                            $isGroup = $groupResults->count() > 1;
                            $groupId = 'group-' . $groupKey;
                        @endphp
                        
                        <!-- Group Header / Single Result -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-6 py-3 cursor-pointer hover:bg-gray-100 transition-colors {{ $isGroup ? '' : '' }}" 
                                 @if($isGroup) onclick="toggleGroup('{{ $groupId }}')" @endif>
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-6 flex-1 min-w-0">
                                        @if($isGroup)
                                        <svg id="icon-{{ $groupId }}" class="w-5 h-5 text-gray-500 transform transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        @else
                                        <div class="w-5"></div>
                                        @endif
                                        <div class="flex items-center gap-6 flex-1 min-w-0 text-sm">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.result_id') }}:</span>
                                                <span class="font-medium text-gray-900">
                                                    @if($isGroup)
                                                        {{ $groupResults->count() }} {{ __('common.tests') }}
                                                    @else
                                                        {{ $firstResult->result_id }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.patient') }}:</span>
                                                <a href="{{ route('patients.show', $firstResult->patient) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                                    {{ $firstResult->patient->name }}
                                                </a>
                                            </div>
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.test') }}:</span>
                                                <span class="text-gray-900">
                                                    @if($isGroup)
                                                        {{ __('common.multiple_tests') }}
                                                    @else
                                                        {{ $firstResult->test->name }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.category') }}:</span>
                                                <span class="text-gray-500">
                                                    @if($isGroup)
                                                        {{ $groupResults->pluck('test.category.name')->unique()->join(', ') }}
                                                    @else
                                                        {{ $firstResult->test->category->name }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.status') }}:</span>
                                                <div>
                                                    @php
                                                        $statuses = $groupResults->pluck('status')->unique();
                                                        $allCompleted = $statuses->count() === 1 && $statuses->first() === 'completed';
                                                        $allPending = $statuses->count() === 1 && $statuses->first() === 'pending';
                                                    @endphp
                                                    @if($allCompleted)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ __('common.completed') }}
                                                        </span>
                                                    @elseif($allPending)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            {{ __('common.pending') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            {{ __('common.mixed_status') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.date') }}:</span>
                                                <span class="text-gray-500">{{ $firstResult->order_date->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 ml-4 flex-shrink-0">
                                        @if($isGroup)
                                        <a href="{{ route('reports.generate', $firstResult) }}" class="text-green-600 hover:text-green-800 font-semibold text-sm" target="_blank" title="{{ __('common.generate_group_pdf') }}">
                                            {{ __('common.report') }}
                                        </a>
                                        @else
                                        <a href="{{ route('test-results.show', $firstResult) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">{{ __('common.view') }}</a>
                                        @if($firstResult->status !== 'completed' || auth()->user()->isAdmin())
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('test-results.edit', $firstResult) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">{{ __('common.edit') }}</a>
                                        @endif
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('reports.generate', $firstResult) }}" class="text-green-600 hover:text-green-800 font-semibold text-sm" target="_blank">{{ __('common.report') }}</a>
                                        @if(auth()->user()->isAdmin())
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('test-results.destroy', $firstResult) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }} {{ __('common.cannot_undo') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">{{ __('common.delete') }}</button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Expandable Group Content -->
                            @if($isGroup)
                            <div id="{{ $groupId }}" class="hidden border-t border-gray-200 bg-white">
                                <div class="px-6 py-4">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.result_id') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.test') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.category') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.status') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($groupResults as $result)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $result->result_id }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $result->test->name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $result->test->category->name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($result->status === 'completed') bg-green-100 text-green-800
                                                        @elseif($result->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                        @elseif($result->status === 'pending') bg-gray-100 text-gray-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        @if($result->status === 'completed')
                                                            {{ __('common.completed') }}
                                                        @elseif($result->status === 'in_progress')
                                                            {{ __('common.in_progress') }}
                                                        @elseif($result->status === 'pending')
                                                            {{ __('common.pending') }}
                                                        @else
                                                            {{ __('common.cancelled') }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ route('test-results.show', $result) }}" class="text-blue-600 hover:text-blue-800">{{ __('common.view') }}</a>
                                                        @if($result->status !== 'completed' || auth()->user()->isAdmin())
                                                        <span class="text-gray-300">|</span>
                                                        <a href="{{ route('test-results.edit', $result) }}" class="text-indigo-600 hover:text-indigo-800">{{ __('common.edit') }}</a>
                                                        @endif
                                                        @if(auth()->user()->isAdmin())
                                                        <span class="text-gray-300">|</span>
                                                        <form action="{{ route('test-results.destroy', $result) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }} {{ __('common.cannot_undo') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800">{{ __('common.delete') }}</button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    @empty
                    <div class="text-center py-8 text-sm text-gray-500">
                        {{ __('common.no_test_results') }}
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleGroup(groupId) {
    const group = document.getElementById(groupId);
    const icon = document.getElementById('icon-' + groupId);
    
    if (group.classList.contains('hidden')) {
        group.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        group.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
@endsection

