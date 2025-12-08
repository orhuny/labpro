@extends('layouts.app')

@section('title', __('common.dashboard'))
@section('page-title', __('common.dashboard'))

@section('content')
<div class="px-6 py-6">
    <!-- Welcome Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ __('common.welcome_back') }}, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-600 text-sm">{{ __('common.overview') }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('common.total_patients') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_patients'] }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-3 shadow-lg">
                    <svg class="text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width: 20px; height: 20px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('common.total_tests') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_tests'] }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-3 shadow-lg">
                    <svg class="text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width: 20px; height: 20px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('common.today_results') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['today_results'] }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-3 shadow-lg">
                    <svg class="text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width: 20px; height: 20px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('common.pending_results') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_results'] }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-3 shadow-lg">
                    <svg class="text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width: 20px; height: 20px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="stat-card border-l-green-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('common.completed_today') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['completed_today'] }}</p>
        </div>
        <div class="stat-card border-l-red-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('common.abnormal_results') }} (7 {{ __('common.days') }})</p>
            <p class="text-3xl font-bold text-red-600">{{ $stats['abnormal_results'] }}</p>
        </div>
        <div class="stat-card border-l-blue-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('common.test_categories') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_categories'] }}</p>
        </div>
    </div>

    <!-- Recent Test Results -->
    <div class="card mb-8">
        <div class="card-header">
            <h3 class="text-xl font-bold text-white uppercase tracking-wide">{{ __('common.recent_test_results') }}</h3>
        </div>
        <div class="card-body">
            @if($recentResults->count() > 0)
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('common.result_id') }}</th>
                            <th>{{ __('common.patient') }}</th>
                            <th>{{ __('common.test') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('common.date') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentResults as $result)
                        <tr>
                            <td class="font-semibold">{{ $result->result_id }}</td>
                            <td>{{ optional($result->patient)->name ?? '-' }}</td>
                            <td>{{ optional($result->test)->name ?? '-' }}</td>
                            <td>
                                @if($result->status == 'completed')
                                    <span class="badge badge-success">{{ __('common.completed') }}</span>
                                @elseif($result->status == 'in_progress')
                                    <span class="badge badge-warning">{{ __('common.in_progress') }}</span>
                                @else
                                    <span class="badge badge-gray">{{ __('common.pending') }}</span>
                                @endif
                            </td>
                            <td>{{ $result->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('test-results.show', $result) }}" class="text-blue-600 hover:text-blue-800 font-semibold">{{ __('common.view') }}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-gray-500 py-8">{{ __('common.no_test_results') }}</p>
            @endif
        </div>
    </div>

    <!-- Category Statistics -->
    @if($categoryStats->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-bold text-white uppercase tracking-wide">{{ __('common.top_test_categories') }}</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($categoryStats as $category)
                <div class="border-2 border-blue-300 rounded-xl p-6 bg-gradient-to-br from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 transition-all shadow-md hover:shadow-lg text-center">
                    <h4 class="font-bold text-blue-900 text-lg mb-2">{{ $category->name }}</h4>
                    <p class="text-sm font-semibold text-blue-700">{{ $category->test_results_count }} test results</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
