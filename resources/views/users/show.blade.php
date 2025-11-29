@extends('layouts.app')

@section('title', __('common.user_details'))
@section('page-title', __('common.user_details'))

@section('content')
<div class="px-8 py-6">
    <!-- Action Buttons -->
    <div class="flex justify-end gap-4 mb-6">
        <a href="{{ route('users.edit', $user) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-md hover:shadow-lg">
            {{ __('common.edit') }}
        </a>
        @if($user->id !== auth()->id())
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }} {{ __('common.cannot_undo') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-md hover:shadow-lg">
                {{ __('common.delete') }}
            </button>
        </form>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- User Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-bold text-white uppercase tracking-wide">{{ __('common.user_information') }}</h3>
            </div>
            <div class="card-body">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.name') }}</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.email') }}</dt>
                        <dd class="text-lg text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.role') }}</dt>
                        <dd>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
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
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.created_at') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.updated_at') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-bold text-white uppercase tracking-wide">{{ __('common.statistics') }}</h3>
            </div>
            <div class="card-body">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.ordered_test_results') }}</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $user->orderedTestResults->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.performed_test_results') }}</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $user->performedTestResults->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Test Results -->
    @if($user->orderedTestResults->count() > 0 || $user->performedTestResults->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-bold text-white uppercase tracking-wide">{{ __('common.test_results') }}</h3>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.result_id') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.patient') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.test') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->orderedTestResults->merge($user->performedTestResults)->unique('id') as $result)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $result->result_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('patients.show', $result->patient) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $result->patient->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result->test->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($result->status === 'completed')
                                    <span class="badge badge-success">{{ __('common.completed') }}</span>
                                @elseif($result->status === 'in_progress')
                                    <span class="badge badge-warning">{{ __('common.in_progress') }}</span>
                                @else
                                    <span class="badge badge-gray">{{ __('common.pending') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->order_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('test-results.show', $result) }}" class="text-blue-600 hover:text-blue-800 font-semibold">{{ __('common.view') }}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

