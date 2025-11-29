@extends('layouts.app')

@section('title', __('common.add_patient'))
@section('page-title', __('common.patient_registration'))

@section('content')
<div class="px-8 py-6">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('patients.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                    <!-- Basic Information -->
                    <div class="space-y-5 bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-xl border-2 border-emerald-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-emerald-400 uppercase tracking-wide">{{ __('common.basic_information') }}</h3>
                        
                        <div class="form-group">
                            <label for="name" class="form-label">{{ __('common.name') }} *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-input">
                            </div>

                        <div class="form-group">
                            <label for="date_of_birth" class="form-label">{{ __('common.date_of_birth') }}</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="age" class="form-label">{{ __('common.age') }}</label>
                            <input type="number" name="age" id="age" value="{{ old('age') }}" min="0" max="150" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="gender" class="form-label">{{ __('common.gender') }}</label>
                            <select name="gender" id="gender" class="form-select">
                                    <option value="">{{ __('common.select_gender') }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('common.male') }}</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('common.female') }}</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('common.other') }}</option>
                                </select>
                            </div>
                        </div>

                    <!-- Contact Information -->
                    <div class="space-y-5 bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-xl border-2 border-emerald-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-emerald-400 uppercase tracking-wide">{{ __('common.contact_information') }}</h3>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">{{ __('common.phone') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">{{ __('common.email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">{{ __('common.address') }}</label>
                            <textarea name="address" id="address" rows="3" class="form-textarea">{{ old('address') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="city" class="form-label">{{ __('common.city') }}</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="state" class="form-label">{{ __('common.state') }}</label>
                                <input type="text" name="state" id="state" value="{{ old('state') }}" class="form-input">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="postal_code" class="form-label">{{ __('common.postal_code') }}</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="country" class="form-label">{{ __('common.country') }}</label>
                                <input type="text" name="country" id="country" value="{{ old('country') }}" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="mt-8 pt-8 border-t-4 border-emerald-300 max-w-4xl mx-auto bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-xl border-2 border-blue-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-blue-400 uppercase tracking-wide text-center">{{ __('common.medical_information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="form-group">
                            <label for="doctor_name" class="form-label">{{ __('common.doctor_name') }}</label>
                            <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name') }}" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="doctor_referral" class="form-label">{{ __('common.doctor_referral') }}</label>
                            <input type="text" name="doctor_referral" id="doctor_referral" value="{{ old('doctor_referral') }}" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="medical_history" class="form-label">{{ __('common.medical_history') }}</label>
                        <textarea name="medical_history" id="medical_history" rows="3" class="form-textarea">{{ old('medical_history') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="allergies" class="form-label">{{ __('common.allergies') }}</label>
                        <textarea name="allergies" id="allergies" rows="2" class="form-textarea">{{ old('allergies') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">{{ __('common.notes') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="form-textarea">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-center gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('patients.index') }}" class="btn-secondary">
                        {{ __('common.cancel') }}
                    </a>
                    <button type="submit" class="btn-primary">
                        {{ __('common.create_patient') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

