@extends('layouts.app')

@section('title', __('common.edit_test_result'))
@section('page-title', __('common.edit_test_result'))

@section('header')
<h1 class="text-4xl font-bold text-white mb-2">{{ __('common.edit_test_result') }}</h1>
<p class="text-emerald-100">{{ $testResult->result_id }} - {{ $testResult->patient->name }}</p>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="card mb-6">
        <div class="card-body max-w-5xl mx-auto">

                <form action="{{ route('test-results.update', $testResult) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="sample_collection_date" class="block text-sm font-medium text-gray-700">{{ __('common.sample_collection_date') }}</label>
                            <input type="date" name="sample_collection_date" id="sample_collection_date" 
                                   value="{{ old('sample_collection_date', $testResult->sample_collection_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="result_date" class="block text-sm font-medium text-gray-700">{{ __('common.result_date') }}</label>
                            <input type="date" name="result_date" id="result_date" 
                                   value="{{ old('result_date', $testResult->result_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('common.status') }} *</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pending" {{ old('status', $testResult->status ?? 'completed') == 'pending' ? 'selected' : '' }}>{{ __('common.pending') }}</option>
                                <option value="in_progress" {{ old('status', $testResult->status ?? 'completed') == 'in_progress' ? 'selected' : '' }}>{{ __('common.in_progress') }}</option>
                                <option value="completed" {{ old('status', $testResult->status ?? 'completed') == 'completed' ? 'selected' : '' }}>{{ __('common.completed') }}</option>
                                <option value="cancelled" {{ old('status', $testResult->status ?? 'completed') == 'cancelled' ? 'selected' : '' }}>{{ __('common.cancelled') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('common.notes') }}</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $testResult->notes) }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="doctor_remarks" class="block text-sm font-medium text-gray-700">{{ __('common.doctor_remarks') }}</label>
                        <textarea name="doctor_remarks" id="doctor_remarks" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('doctor_remarks', $testResult->doctor_remarks) }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="technician_notes" class="block text-sm font-medium text-gray-700">{{ __('common.technician_notes') }}</label>
                        <textarea name="technician_notes" id="technician_notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('technician_notes', $testResult->technician_notes) }}</textarea>
                    </div>

                    <!-- Test Parameters Values -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('common.test_results') }}</h3>
                        <div class="space-y-4">
                            @foreach($testResult->test->activeParameters as $parameter)
                            @php
                                $existingValue = $testResult->values->firstWhere('test_parameter_id', $parameter->id);
                                $range = $parameter->getNormalRange($testResult->patient->gender);
                            @endphp
                            <div class="border rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $parameter->name }}</label>
                                        @if($parameter->reference_ranges && count($parameter->reference_ranges) > 0)
                                            <div class="space-y-1.5">
                                                @foreach($parameter->reference_ranges as $refRange)
                                                    <div class="text-xs">
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
                                                            <div class="text-gray-600">
                                                                <span class="font-medium text-gray-700">{{ $refRange['label'] ?? '' }}:</span>
                                                                <span class="ml-1">{{ $displayValue }}</span>
                                                            </div>
                                                        @elseif($refRange['label'] ?? '')
                                                            <div class="text-gray-600">
                                                                <span class="font-medium text-gray-700">{{ $refRange['label'] }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($range['min'] !== null || $range['max'] !== null)
                                            <p class="text-xs text-gray-500">
                                                Ref: {{ $range['min'] ?? 'N/A' }} - {{ $range['max'] ?? 'N/A' }}
                                                @if($parameter->unit) ({{ $parameter->unit }}) @endif
                                            </p>
                                        @elseif($parameter->unit)
                                            <p class="text-xs text-gray-500">({{ $parameter->unit }})</p>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="hidden" name="values[{{ $loop->index }}][parameter_id]" value="{{ $parameter->id }}">
                                        <input type="text" 
                                               name="values[{{ $loop->index }}][value]" 
                                               value="{{ old("values.{$loop->index}.value", $existingValue?->value) }}"
                                               placeholder="{{ __('common.value') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="text" 
                                               name="values[{{ $loop->index }}][notes]" 
                                               value="{{ old("values.{$loop->index}.notes", $existingValue?->notes) }}"
                                               placeholder="{{ __('common.notes') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div class="flex items-center">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   name="values[{{ $loop->index }}][is_outside_normal_range]" 
                                                   value="1"
                                                   {{ old("values.{$loop->index}.is_outside_normal_range", $existingValue?->is_outside_normal_range) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ __('common.outside_normal_range') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-center gap-4">
                        <a href="{{ route('test-results.show', $testResult) }}" class="btn-secondary">
                            {{ __('common.cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            {{ __('common.update_test_result') }}
                        </button>
                        @if($testResult->values->count() > 0)
                        <a href="{{ route('reports.generate', $testResult) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-lg" target="_blank">
                            {{ __('common.generate_pdf') }}
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

