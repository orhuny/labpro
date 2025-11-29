@extends('layouts.app')

@section('title', __('common.order_new_tests'))

@section('header')
<h1 class="text-4xl font-bold text-white mb-2">{{ __('common.order_new_tests') }}</h1>
<p class="text-emerald-100">{{ __('common.create_new_test_order') }}</p>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="card">
        <div class="card-body max-w-3xl mx-auto">

                <form action="{{ route('test-results.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">{{ __('common.patient') }} *</label>
                            <div class="mt-1 flex gap-2">
                                <input type="text" 
                                       id="patient_search" 
                                       placeholder="{{ __('common.search_patient') }}..."
                                       class="flex-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <select name="patient_id" id="patient_id" required
                                        class="flex-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('common.select_patient') }}</option>
                                    @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" 
                                            data-name="{{ strtolower($patient->name) }}" 
                                            data-patient-id="{{ strtolower($patient->patient_id) }}"
                                            {{ old('patient_id', request('patient_id')) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} ({{ $patient->patient_id }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                <a href="{{ route('patients.create') }}" class="text-blue-600 hover:text-blue-900">{{ __('common.add_new_patient') }}</a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('common.select_tests') }} *</label>
                            <div class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto bg-white">
                                @php
                                    $testsByCategory = $tests->groupBy(function($test) {
                                        return $test->category->name ?? __('common.uncategorized');
                                    });
                                    $oldTestIds = old('test_ids', []);
                                @endphp
                                @foreach($testsByCategory as $categoryName => $categoryTests)
                                    <div class="mb-4 last:mb-0">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-2 pb-1 border-b border-gray-200">{{ $categoryName }}</h4>
                                        <div class="space-y-2">
                                            @foreach($categoryTests as $test)
                                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                                <input type="checkbox" 
                                                       name="test_ids[]" 
                                                       value="{{ $test->id }}"
                                                       {{ in_array($test->id, $oldTestIds) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-3 text-sm text-gray-700">{{ $test->name }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('test_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">{{ __('common.select_multiple_tests_note') }}</p>
                        </div>

                        <div>
                            <label for="order_date" class="block text-sm font-medium text-gray-700">{{ __('common.order_date') }} *</label>
                            <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="sample_collection_date" class="block text-sm font-medium text-gray-700">{{ __('common.sample_collection_date') }}</label>
                            <input type="date" name="sample_collection_date" id="sample_collection_date" value="{{ old('sample_collection_date') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('common.notes') }}</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center gap-4">
                        <a href="{{ route('test-results.index') }}" class="btn-secondary">
                            {{ __('common.cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            {{ __('common.order_tests') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientSearch = document.getElementById('patient_search');
    const patientSelect = document.getElementById('patient_id');
    const allOptions = Array.from(patientSelect.options).slice(1); // İlk option'ı (placeholder) hariç tut
    
    patientSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Önce tüm option'ları göster
        allOptions.forEach(option => {
            option.style.display = '';
        });
        
        // Eğer arama terimi varsa filtrele
        if (searchTerm) {
            allOptions.forEach(option => {
                const name = option.getAttribute('data-name') || '';
                const patientId = option.getAttribute('data-patient-id') || '';
                const text = option.textContent.toLowerCase();
                
                // İsim, hasta ID veya tam metin içinde arama yap
                if (name.includes(searchTerm) || 
                    patientId.includes(searchTerm) || 
                    text.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    });
});
</script>
@endsection

