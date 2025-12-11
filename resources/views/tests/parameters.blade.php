@extends('layouts.app')

@section('title', __('common.manage_parameters'))
@section('page-title', __('common.manage_parameters'))

@section('content')
<div class="px-8 py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('common.manage_parameters') }}: {{ $test->name }}</h2>
            <p class="text-gray-600 text-sm mt-1">{{ __('common.test_category') }}: {{ $test->category->name }}</p>
        </div>
        <a href="{{ route('tests.show', $test) }}" class="btn-secondary">
            {{ __('common.back_to_test') }}
        </a>
    </div>

    <!-- Add Parameter Form -->
    <div class="card mb-6">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('common.add_new_parameter') }}</h3>
            <form action="{{ route('tests.parameters.store', $test) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="name" class="form-label">{{ __('common.parameter_name') }} *</label>
                        <input type="text" name="name" id="name" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="code" class="form-label">{{ __('common.parameter_code') }}</label>
                        <input type="text" name="code" id="code" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="unit" class="form-label">{{ __('common.unit') }}</label>
                        <input type="text" name="unit" id="unit" placeholder="e.g., g/dL, cells/μL" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="value_type" class="form-label">{{ __('common.value_type') }} *</label>
                        <select name="value_type" id="value_type" required class="form-select">
                            <option value="numeric">{{ __('common.numeric') }}</option>
                            <option value="text">{{ __('common.text') }}</option>
                            <option value="boolean">{{ __('common.boolean') }}</option>
                            <option value="calculated">{{ __('common.calculated') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sort_order" class="form-label">{{ __('common.sort_order') }}</label>
                        <input type="number" name="sort_order" id="sort_order" value="0" min="0" class="form-input">
                    </div>
                </div>
                
                <!-- Reference Ranges Section -->
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-4">{{ __('common.reference_ranges') }}</h4>
                    <div id="referenceRangesContainer">
                        <!-- Reference ranges will be added here dynamically -->
                    </div>
                    <button type="button" onclick="addReferenceRange()" class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-semibold">
                        + {{ __('common.add_reference_range') }}
                    </button>
                </div>
                
                <!-- Rich HTML Reference (optional) -->
                <div class="mt-6 border border-dashed border-gray-200 rounded-lg p-4 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-700 mb-2">Referans HTML (opsiyonel)</h4>
                    <p class="text-xs text-gray-500 mb-2">Word/Excel tablosunu veya biçimli metni buraya yapıştırabilirsiniz. PDF'te referans aralığı sütununda aynen gösterilir.</p>
                    <textarea name="reference_html" id="reference_html" rows="4" class="form-textarea" placeholder="<table>...</table>"></textarea>
                </div>
                
                <div class="mt-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">{{ __('common.active') }}</span>
                    </label>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn-primary">
                        {{ __('common.add_parameter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Parameters -->
    <div class="card">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('common.existing_parameters') }}</h3>
            @if($test->parameters->count() > 0)
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.parameter_name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.unit') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.reference_ranges') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.value_type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($test->parameters as $parameter)
                        <tr id="parameter-row-{{ $parameter->id }}" class="parameter-display-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $parameter->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $parameter->unit ?? __('common.none') }}</td>
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
                                                    
                                                    // Priority: min/max > value
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
                                    {{ __('common.none') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($parameter->value_type === 'numeric')
                                    {{ __('common.numeric') }}
                                @elseif($parameter->value_type === 'text')
                                    {{ __('common.text') }}
                                @elseif($parameter->value_type === 'boolean')
                                    {{ __('common.boolean') }}
                                @else
                                    {{ __('common.calculated') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <button onclick="toggleEdit({{ $parameter->id }})" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        {{ __('common.edit') }}
                                    </button>
                                    @if(auth()->user()->isAdmin())
                                    @php
                                        $resultCount = $parameter->resultValues->count();
                                    @endphp
                                    @if($resultCount > 0)
                                        <span class="text-gray-500 text-xs font-medium" title="{{ __('common.cannot_delete_parameter', ['name' => $parameter->name, 'count' => $resultCount]) }}">
                                            {{ __('common.assigned') }} ({{ $resultCount }})
                                        </span>
                                    @else
                                        <form action="{{ route('tests.parameters.destroy', [$test, $parameter]) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">{{ __('common.delete') }}</button>
                                        </form>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <!-- Edit Form Row (Hidden by default) -->
                        <tr id="parameter-edit-row-{{ $parameter->id }}" class="hidden bg-blue-50">
                            <td colspan="5" class="px-6 py-4">
                                <form action="{{ route('tests.parameters.update', [$test, $parameter]) }}" method="POST" class="bg-white p-4 rounded-lg border border-blue-200">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="text-lg font-semibold text-gray-800">{{ __('common.edit_parameter') }}: {{ $parameter->name }}</h4>
                                        <button type="button" onclick="toggleEdit({{ $parameter->id }})" class="text-gray-500 hover:text-gray-700">
                                            {{ __('common.cancel') }}
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label for="edit_name_{{ $parameter->id }}" class="form-label">{{ __('common.parameter_name') }} *</label>
                                            <input type="text" name="name" id="edit_name_{{ $parameter->id }}" value="{{ $parameter->name }}" required class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_code_{{ $parameter->id }}" class="form-label">{{ __('common.parameter_code') }}</label>
                                            <input type="text" name="code" id="edit_code_{{ $parameter->id }}" value="{{ $parameter->code ?? '' }}" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_unit_{{ $parameter->id }}" class="form-label">{{ __('common.unit') }}</label>
                                            <input type="text" name="unit" id="edit_unit_{{ $parameter->id }}" value="{{ $parameter->unit ?? '' }}" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_value_type_{{ $parameter->id }}" class="form-label">{{ __('common.value_type') }} *</label>
                                            <select name="value_type" id="edit_value_type_{{ $parameter->id }}" required class="form-select">
                                                <option value="numeric" {{ $parameter->value_type === 'numeric' ? 'selected' : '' }}>{{ __('common.numeric') }}</option>
                                                <option value="text" {{ $parameter->value_type === 'text' ? 'selected' : '' }}>{{ __('common.text') }}</option>
                                                <option value="boolean" {{ $parameter->value_type === 'boolean' ? 'selected' : '' }}>{{ __('common.boolean') }}</option>
                                                <option value="calculated" {{ $parameter->value_type === 'calculated' ? 'selected' : '' }}>{{ __('common.calculated') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_sort_order_{{ $parameter->id }}" class="form-label">{{ __('common.sort_order') }}</label>
                                            <input type="number" name="sort_order" id="edit_sort_order_{{ $parameter->id }}" value="{{ $parameter->sort_order ?? 0 }}" min="0" class="form-input">
                                        </div>
                                    </div>
                                    
                                    <!-- Reference Ranges Section -->
                                    <div class="mt-6 border-t border-gray-200 pt-6">
                                        <h4 class="text-md font-semibold text-gray-700 mb-4">{{ __('common.reference_ranges') }}</h4>
                                        <div id="editReferenceRangesContainer_{{ $parameter->id }}">
                                            @if($parameter->reference_ranges && count($parameter->reference_ranges) > 0)
                                                @foreach($parameter->reference_ranges as $index => $range)
                                                    <div class="reference-range-item mb-3 p-3 bg-gray-50 rounded border border-gray-200">
                                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                                            <div>
                                                                <label class="form-label text-xs">{{ __('common.label') }}</label>
                                                                <input type="text" name="reference_ranges[{{ $index }}][label]" value="{{ $range['label'] ?? '' }}" class="form-input text-sm" required>
                                                            </div>
                                                            <div>
                                                                <label class="form-label text-xs">{{ __('common.prefix') }} (örn: &lt;)</label>
                                                                <input type="text" name="reference_ranges[{{ $index }}][prefix]" value="{{ $range['prefix'] ?? '' }}" class="form-input text-sm" placeholder="<, >, ≤, ≥">
                                                            </div>
                                                            <div>
                                                                <label class="form-label text-xs">{{ __('common.min') }}</label>
                                                                <input type="number" name="reference_ranges[{{ $index }}][min]" value="{{ $range['min'] ?? '' }}" step="0.01" class="form-input text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="form-label text-xs">{{ __('common.max') }}</label>
                                                                <input type="number" name="reference_ranges[{{ $index }}][max]" value="{{ $range['max'] ?? '' }}" step="0.01" class="form-input text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="form-label text-xs">{{ __('common.value') }} (örn: -)</label>
                                                                <input type="text" name="reference_ranges[{{ $index }}][value]" value="{{ $range['value'] ?? '' }}" class="form-input text-sm" placeholder="-">
                                                            </div>
                                                        </div>
                                                        <button type="button" onclick="removeReferenceRange(this)" class="mt-2 text-xs text-red-600 hover:text-red-800">{{ __('common.remove') }}</button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" onclick="addEditReferenceRange({{ $parameter->id }})" class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                            + {{ __('common.add_reference_range') }}
                                        </button>
                                    </div>
                                    
                                    <!-- Rich HTML Reference (optional) -->
                                    <div class="mt-6 border border-dashed border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <h4 class="text-md font-semibold text-gray-700 mb-2">Referans HTML (opsiyonel)</h4>
                                        <p class="text-xs text-gray-500 mb-2">Word/Excel tablosunu veya biçimli metni buraya yapıştırabilirsiniz. PDF'te referans aralığı sütununda aynen gösterilir.</p>
                                        <textarea name="reference_html" id="edit_reference_html_{{ $parameter->id }}" rows="4" class="form-textarea" placeholder="<table>...">{{ old('reference_html', $parameter->reference_html) }}</textarea>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="is_active" id="edit_is_active_{{ $parameter->id }}" value="1" {{ $parameter->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-600">{{ __('common.active') }}</span>
                                        </label>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" onclick="toggleEdit({{ $parameter->id }})" class="btn-secondary">
                                            {{ __('common.cancel') }}
                                        </button>
                                        <button type="submit" class="btn-primary">
                                            {{ __('common.save') }}
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-500">{{ __('common.no_parameters_defined') }}</p>
            @endif
        </div>
    </div>
</div>

<script>
let referenceRangeIndex = 0;

function toggleEdit(parameterId) {
    const displayRow = document.getElementById('parameter-row-' + parameterId);
    const editRow = document.getElementById('parameter-edit-row-' + parameterId);
    
    if (editRow.classList.contains('hidden')) {
        // Show edit form, hide display row
        displayRow.classList.add('hidden');
        editRow.classList.remove('hidden');
    } else {
        // Show display row, hide edit form
        displayRow.classList.remove('hidden');
        editRow.classList.add('hidden');
    }
}

function addReferenceRange() {
    const container = document.getElementById('referenceRangesContainer');
    const index = referenceRangeIndex++;
    const labelText = {!! json_encode(__('common.label')) !!};
    const prefixText = {!! json_encode(__('common.prefix')) !!};
    const minText = {!! json_encode(__('common.min')) !!};
    const maxText = {!! json_encode(__('common.max')) !!};
    const valueText = {!! json_encode(__('common.value')) !!};
    const removeText = {!! json_encode(__('common.remove')) !!};
    const html = `
        <div class="reference-range-item mb-3 p-3 bg-gray-50 rounded border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="form-label text-xs">${labelText}</label>
                    <input type="text" name="reference_ranges[${index}][label]" class="form-input text-sm" required>
                </div>
                <div>
                    <label class="form-label text-xs">${prefixText} (örn: &lt;)</label>
                    <input type="text" name="reference_ranges[${index}][prefix]" class="form-input text-sm" placeholder="<, >, ≤, ≥">
                </div>
                <div>
                    <label class="form-label text-xs">${minText}</label>
                    <input type="number" name="reference_ranges[${index}][min]" step="0.01" class="form-input text-sm">
                </div>
                <div>
                    <label class="form-label text-xs">${maxText}</label>
                    <input type="number" name="reference_ranges[${index}][max]" step="0.01" class="form-input text-sm">
                </div>
                <div>
                    <label class="form-label text-xs">${valueText} (örn: -)</label>
                    <input type="text" name="reference_ranges[${index}][value]" class="form-input text-sm" placeholder="-">
                </div>
            </div>
            <button type="button" onclick="removeReferenceRange(this)" class="mt-2 text-xs text-red-600 hover:text-red-800">${removeText}</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function addEditReferenceRange(parameterId) {
    const container = document.getElementById('editReferenceRangesContainer_' + parameterId);
    const existingRanges = container.querySelectorAll('.reference-range-item').length;
    const index = existingRanges;
    const labelText = {!! json_encode(__('common.label')) !!};
    const prefixText = {!! json_encode(__('common.prefix')) !!};
    const minText = {!! json_encode(__('common.min')) !!};
    const maxText = {!! json_encode(__('common.max')) !!};
    const valueText = {!! json_encode(__('common.value')) !!};
    const removeText = {!! json_encode(__('common.remove')) !!};
    const html = `
        <div class="reference-range-item mb-3 p-3 bg-gray-50 rounded border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="form-label text-xs">${labelText}</label>
                    <input type="text" name="reference_ranges[${index}][label]" class="form-input text-sm" required>
                </div>
                <div>
                    <label class="form-label text-xs">${prefixText} (örn: &lt;)</label>
                    <input type="text" name="reference_ranges[${index}][prefix]" class="form-input text-sm" placeholder="<, >, ≤, ≥">
                </div>
                <div>
                    <label class="form-label text-xs">${minText}</label>
                    <input type="number" name="reference_ranges[${index}][min]" step="0.01" class="form-input text-sm">
                </div>
                <div>
                    <label class="form-label text-xs">${maxText}</label>
                    <input type="number" name="reference_ranges[${index}][max]" step="0.01" class="form-input text-sm">
                </div>
                <div>
                    <label class="form-label text-xs">${valueText} (örn: -)</label>
                    <input type="text" name="reference_ranges[${index}][value]" class="form-input text-sm" placeholder="-">
                </div>
            </div>
            <button type="button" onclick="removeReferenceRange(this)" class="mt-2 text-xs text-red-600 hover:text-red-800">${removeText}</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function removeReferenceRange(button) {
    button.closest('.reference-range-item').remove();
}
</script>
@endsection
