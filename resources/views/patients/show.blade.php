@extends('layouts.app')

@section('title', __('common.patient_details'))
@section('page-title', __('common.patient_details'))

@section('content')
<div class="px-6 py-6">
    <!-- Action Buttons -->
    <div class="flex gap-3 mb-6">
        <a href="{{ route('patients.edit', $patient) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-md hover:shadow-lg">
            {{ __('common.edit') }}
        </a>
        <a href="{{ route('test-results.create', ['patient_id' => $patient->id]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-md hover:shadow-lg">
            {{ __('common.order_test') }}
        </a>
    </div>

    <!-- Patient Information Card -->
    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-8">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('common.basic_information') }}</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.patient_id') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $patient->patient_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.name') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $patient->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.date_of_birth') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">
                                    {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.age') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $patient->age ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.gender') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ ucfirst($patient->gender ?? 'N/A') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Medical Information -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('common.medical_information') }}</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.doctor_name') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $patient->doctor_name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.doctor_referral') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $patient->doctor_referral ?? 'N/A' }}</dd>
                            </div>
                            @if($patient->medical_history)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.medical_history') }}</dt>
                                <dd class="text-base text-gray-900 whitespace-pre-wrap">{{ $patient->medical_history }}</dd>
                            </div>
                            @endif
                            @if($patient->allergies)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.allergies') }}</dt>
                                <dd class="text-base text-gray-900 whitespace-pre-wrap">{{ $patient->allergies }}</dd>
                            </div>
                            @endif
                            @if($patient->notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.notes') }}</dt>
                                <dd class="text-base text-gray-900 whitespace-pre-wrap">{{ $patient->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Contact Information -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('common.contact_information') }}</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.phone') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $patient->phone ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.email') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">
                                    @if($patient->email)
                                        <a href="mailto:{{ $patient->email }}" class="text-blue-600 hover:text-blue-800">{{ $patient->email }}</a>
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('common.address') }}</dt>
                                <dd class="text-base font-semibold text-gray-900">
                                    @if($patient->address || $patient->city || $patient->state || $patient->postal_code || $patient->country)
                                        @if($patient->address){{ $patient->address }}<br>@endif
                                        @if($patient->city || $patient->state || $patient->postal_code)
                                            @if($patient->city){{ $patient->city }}@endif
                                            @if($patient->state), {{ $patient->state }}@endif
                                            @if($patient->postal_code) {{ $patient->postal_code }}@endif<br>
                                        @endif
                                        @if($patient->country){{ $patient->country }}@endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Results Section -->
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="text-xl font-bold text-white uppercase tracking-wide">{{ __('common.test_results') }}</h3>
        </div>
        <div class="card-body">
            @if($patient->testResults->count() > 0)
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Result ID</th>
                            <th>Test</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patient->testResults as $result)
                        <tr>
                            <td class="font-semibold">{{ $result->result_id }}</td>
                            <td>{{ $result->test->name }}</td>
                            <td>{{ $result->test->category->name }}</td>
                            <td>
                                @if($result->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($result->status === 'in_progress')
                                    <span class="badge badge-warning">In Progress</span>
                                @elseif($result->status === 'pending')
                                    <span class="badge badge-gray">Pending</span>
                                @else
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                                @if($result->is_abnormal)
                                    <span class="text-red-600 text-xl font-bold ml-2">*</span>
                                @endif
                            </td>
                            <td>{{ $result->order_date ? $result->order_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('test-results.show', $result) }}" class="text-blue-600 hover:text-blue-800 font-semibold">{{ __('common.view') }}</a>
                                    @if($result->status !== 'completed' || auth()->user()->isAdmin())
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('test-results.edit', $result) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">{{ __('common.edit') }}</a>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('test-results.destroy', $result) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }} {{ __('common.cannot_undo') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">{{ __('common.delete') }}</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg mb-4">{{ __('common.no_test_results') }}</p>
                <a href="{{ route('test-results.create', ['patient_id' => $patient->id]) }}" class="btn-primary inline-block">
                    {{ __('common.order_test') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
