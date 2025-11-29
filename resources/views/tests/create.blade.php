@extends('layouts.app')

@section('title', 'Add New Test')

@section('header')
<h1 class="text-4xl font-bold text-white mb-2">Add New Test</h1>
<p class="text-emerald-100">Create a new test definition</p>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="card">
        <div class="card-body max-w-3xl mx-auto">

                <form action="{{ route('tests.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="test_category_id" class="block text-sm font-medium text-gray-700">Test Category *</label>
                            <select name="test_category_id" id="test_category_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('test_category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Test Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., Complete Blood Count, Glucose">
                        </div>

                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Test Code *</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., CBC, GLU" style="text-transform: uppercase;">
                            <p class="mt-1 text-sm text-gray-500">Unique code for this test</p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Brief description of this test">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                <input type="number" name="price" id="price" value="{{ old('price', 0) }}" step="0.01" min="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="turnaround_time_hours" class="block text-sm font-medium text-gray-700">Turnaround Time (Hours)</label>
                                <input type="number" name="turnaround_time_hours" id="turnaround_time_hours" value="{{ old('turnaround_time_hours') }}" min="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-600">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center gap-4">
                        <a href="{{ route('tests.index') }}" class="btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary">
                            Create Test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

