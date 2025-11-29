@extends('layouts.app')

@section('title', 'Test Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Test: {{ $test->name }}</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('tests.edit', $test) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <a href="{{ route('tests.parameters', $test) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Manage Parameters
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Test Information</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Test Name</dt>
                                <dd class="text-sm text-gray-900">{{ $test->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="text-sm text-gray-900">{{ $test->category->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Code</dt>
                                <dd class="text-sm text-gray-900">{{ $test->code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Price</dt>
                                <dd class="text-sm text-gray-900">${{ number_format($test->price, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $test->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $test->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Additional Information</h3>
                        <dl class="space-y-2">
                            @if($test->description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="text-sm text-gray-900">{{ $test->description }}</dd>
                            </div>
                            @endif
                            @if($test->turnaround_time_hours)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Turnaround Time</dt>
                                <dd class="text-sm text-gray-900">{{ $test->turnaround_time_hours }} hours</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                                <dd class="text-sm text-gray-900">{{ $test->sort_order }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Parameters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Test Parameters</h3>
                    <a href="{{ route('tests.parameters', $test) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Manage Parameters
                    </a>
                </div>
                @if($test->parameters->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parameter Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Normal Range</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($test->parameters as $parameter)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $parameter->name }}</td>
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
                                                            } else {
                                                                $displayValue = '';
                                                            }
                                                            
                                                            if ($displayValue && $parameter->unit) {
                                                                $displayValue .= ' ' . $parameter->unit;
                                                            }
                                                        } 
                                                        elseif ($refValue !== null && $refValue !== '' && strtolower(trim($refValue)) !== strtolower(trim($parameter->unit ?? ''))) {
                                                            $displayValue = $refValue;
                                                        } else {
                                                            $displayValue = '';
                                                        }
                                                    @endphp
                                                    @if($displayValue)
                                                        {{ $displayValue }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($parameter->value_type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $parameter->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $parameter->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-500">No parameters defined for this test. <a href="{{ route('tests.parameters', $test) }}" class="text-blue-600 hover:text-blue-900">Add parameters</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

