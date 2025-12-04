@extends('layouts.app')

@section('title', __('common.test_result'))

@section('header')
<div class="flex flex-col md:flex-row justify-between items-center gap-4">
    <div>
        <h1 class="text-4xl font-bold text-white mb-2">{{ __('common.test_result') }}</h1>
        <p class="text-emerald-100">{{ $testResult->result_id }} - {{ $testResult->test->name }}</p>
    </div>
    <div class="flex gap-2">
        @if($testResult->status !== 'completed' || auth()->user()->isAdmin())
        <a href="{{ route('test-results.edit', $testResult) }}" class="bg-white hover:bg-emerald-50 text-emerald-600 font-semibold py-3 px-6 rounded-lg transition-colors shadow-lg">
            {{ __('common.edit') }}
        </a>
        @endif
        <a href="{{ route('reports.generate', $testResult) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-lg" target="_blank">
            {{ __('common.generate_pdf') }}
        </a>
        @if(auth()->user()->isAdmin())
        <form action="{{ route('test-results.destroy', $testResult) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }} {{ __('common.cannot_undo') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-lg">
                {{ __('common.delete') }}
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="card mb-6">
        <div class="card-body max-w-5xl mx-auto">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center md:text-left">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('common.patient_information') }}</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.name') }}</dt>
                                <dd class="text-sm text-gray-900">
                                    <a href="{{ route('patients.show', $testResult->patient) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $testResult->patient->name }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.patient_id') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $testResult->patient->patient_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.age') }} / {{ __('common.gender') }}</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $testResult->patient->age ?? 'N/A' }} / {{ ucfirst($testResult->patient->gender ?? 'N/A') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('common.test_information') }}</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.status') }}</dt>
                                <dd class="text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($testResult->status === 'completed') bg-green-100 text-green-800
                                        @elseif($testResult->status === 'in_progress') bg-yellow-100 text-yellow-800
                                        @elseif($testResult->status === 'pending') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($testResult->status === 'completed')
                                            {{ __('common.completed') }}
                                        @elseif($testResult->status === 'in_progress')
                                            {{ __('common.in_progress') }}
                                        @elseif($testResult->status === 'pending')
                                            {{ __('common.pending') }}
                                        @else
                                            {{ __('common.cancelled') }}
                                        @endif
                                    </span>
                                    @if($testResult->is_abnormal)
                                    <span class="ml-2 text-red-600 text-xl font-bold">
                                        *
                                    </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.order_date') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $testResult->order_date->format('M d, Y') }}</dd>
                            </div>
                            @if($testResult->sample_collection_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.sample_collection_date') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $testResult->sample_collection_date->format('M d, Y') }}</dd>
                            </div>
                            @endif
                            @if($testResult->result_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.result_date') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $testResult->result_date->format('M d, Y') }}</dd>
                            </div>
                            @endif
                            @if($testResult->orderedBy)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.ordered_by') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $testResult->orderedBy->name }}</dd>
                            </div>
                            @endif
                            @if($testResult->performedBy)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.performed_by') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $testResult->performedBy->name }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                @if($testResult->notes || $testResult->doctor_remarks || $testResult->technician_notes)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('common.notes_remarks') }}</h3>
                    <dl class="space-y-2">
                        @if($testResult->notes)
                        <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.notes') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $testResult->notes }}</dd>
                        </div>
                        @endif
                        @if($testResult->doctor_remarks)
                        <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.doctor_remarks') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $testResult->doctor_remarks }}</dd>
                        </div>
                        @endif
                        @if($testResult->technician_notes)
                        <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('common.technician_notes') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $testResult->technician_notes }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif
            </div>
        </div>

    <!-- Test Results Values -->
    <div class="card">
        <div class="card-body max-w-5xl mx-auto">
            <h3 class="section-title text-center">{{ __('common.test_results') }}</h3>
                @php
                    // Sadece değeri girilmiş olan sonuçları göster
                    $displayValues = $testResult->values->filter(function ($v) {
                        return $v->value !== null && $v->value !== '';
                    });
                @endphp
                @if($displayValues->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.parameters') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.value') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.unit') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.reference_range') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($displayValues as $value)
                            @php
                                $parameter = $value->parameter;
                                $range = $parameter->getNormalRange($testResult->patient->gender);
                            @endphp
                            <tr class="{{ $value->is_outside_normal_range ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $parameter->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $value->value ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $parameter->unit ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($parameter->reference_ranges && count($parameter->reference_ranges) > 0)
                                        <div class="space-y-1">
                                            @foreach($parameter->reference_ranges as $refRange)
                                                <div class="text-xs">
                                                    <span class="font-medium">{{ $refRange['label'] ?? '' }}:</span>
                                                    @php
                                                        $refValue = $refRange['value'] ?? null;
                                                        $min = $refRange['min'] ?? null;
                                                        $max = $refRange['max'] ?? null;
                                                        $prefix = $refRange['prefix'] ?? '';
                                                        
                                                        $hasMinMax = ($min !== null && $min !== '') || ($max !== null && $max !== '') || ($min === 0 || $min === '0') || ($max === 0 || $max === '0');
                                                        $hasValue = $refValue !== null && $refValue !== '' && strtolower(trim($refValue)) !== strtolower(trim($parameter->unit ?? ''));
                                                        
                                                        $displayValue = '';
                                                        
                                                        if ($hasMinMax) {
                                                            // Format min/max values - handle 0 as valid value
                                                            $minStr = '';
                                                            if ($min !== null && $min !== '') {
                                                                $minStr = number_format((float)$min, 2, '.', '');
                                                                // Remove trailing zeros but keep 0
                                                                $minStr = rtrim(rtrim($minStr, '0'), '.');
                                                                if ($minStr === '') $minStr = '0';
                                                            } elseif ($min === 0 || $min === '0') {
                                                                $minStr = '0';
                                                            }
                                                            
                                                            $maxStr = '';
                                                            if ($max !== null && $max !== '') {
                                                                $maxStr = number_format((float)$max, 2, '.', '');
                                                                // Remove trailing zeros but keep 0
                                                                $maxStr = rtrim(rtrim($maxStr, '0'), '.');
                                                                if ($maxStr === '') $maxStr = '0';
                                                            } elseif ($max === 0 || $max === '0') {
                                                                $maxStr = '0';
                                                            }
                                                            
                                                            if ($minStr !== '' && $maxStr !== '') {
                                                                $displayValue = $prefix . $minStr . '-' . $maxStr;
                                                            } elseif ($minStr !== '') {
                                                                $displayValue = $prefix . $minStr;
                                                            } elseif ($maxStr !== '') {
                                                                $displayValue = $prefix . $maxStr;
                                                            }
                                                            
                                                            // Add unit if we have a numeric range
                                                            if ($displayValue && $parameter->unit) {
                                                                $displayValue .= ' ' . $parameter->unit;
                                                            }
                                                            
                                                            // Add value if it exists
                                                            if ($hasValue) {
                                                                $displayValue .= ($displayValue ? ' (' . $refValue . ')' : $refValue);
                                                            }
                                                        } 
                                                        // If no min/max but we have a value (and it's not just unit info)
                                                        elseif ($hasValue) {
                                                            $displayValue = $refValue;
                                                        }
                                                    @endphp
                                                    @if($displayValue)
                                                        {{ $displayValue }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($range['min'] !== null || $range['max'] !== null)
                                        {{ $range['min'] ?? 'N/A' }} - {{ $range['max'] ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($value->is_outside_normal_range)
                                    <span class="text-red-600 text-xl font-bold">
                                        *
                                    </span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ __('common.normal') }}
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @if($value->notes)
                            <tr>
                                <td colspan="5" class="px-6 py-2 text-xs text-gray-500 italic">
                                    Note: {{ $value->notes }}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-center gap-4">
                    @if($testResult->status !== 'completed' || auth()->user()->isAdmin())
                    <a href="{{ route('test-results.edit', $testResult) }}" class="btn-primary">
                        {{ __('common.edit') }}
                    </a>
                    @endif
                    <a href="{{ route('reports.generate', $testResult) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-lg" target="_blank">
                        {{ __('common.report') }}
                    </a>
                </div>
                @else
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500 mb-4">{{ __('common.no_test_values') }}</p>
                    @if($testResult->status !== 'completed')
                    <a href="{{ route('test-results.edit', $testResult) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                        {{ __('common.enter_test_values') }}
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

