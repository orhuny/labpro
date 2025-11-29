@extends('layouts.app')

@section('title', __('common.tests'))
@section('page-title', __('common.tests'))

@section('content')
<div class="px-8 py-6">
    <!-- Page Header with Action -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('common.all_tests') }}</h2>
            <p class="text-gray-600 text-sm mt-1">{{ __('common.manage_tests') }}</p>
        </div>
        <a href="{{ route('tests.create') }}" class="btn-primary">
            + {{ __('common.add_test') }}
        </a>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <!-- Filters -->
                <form method="GET" action="{{ route('tests.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="category_id" class="form-label">{{ __('common.filter_by_category') }}</label>
                            <select name="category_id" id="category_id" onchange="this.form.submit()" class="form-select">
                                <option value="">{{ __('common.all_categories') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="search" class="form-label">{{ __('common.search') }}</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="{{ __('common.search') }}..." class="form-input">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="btn-primary w-full">
                                {{ __('common.search') }}
                            </button>
                        </div>
                    </div>
                    @if(request('category_id') || request('search'))
                    <div class="mt-2">
                        <a href="{{ route('tests.index') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('common.clear_filters') }}</a>
                    </div>
                    @endif
                </form>

            <!-- Tests Table -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.test_name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.category') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.code') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.price') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.parameters') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tests as $test)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $test->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($test->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('tests.parameters', $test) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $test->parameters->count() }} {{ __('common.parameters') }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $test->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $test->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('tests.show', $test) }}" class="text-emerald-600 hover:text-emerald-800 font-medium mr-3">{{ __('common.view') }}</a>
                                    <a href="{{ route('tests.edit', $test) }}" class="text-emerald-700 hover:text-emerald-900 font-medium mr-3">{{ __('common.edit') }}</a>
                                    <a href="{{ route('tests.parameters', $test) }}" class="text-green-600 hover:text-green-800 font-medium mr-3">{{ __('common.parameters') }}</a>
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('tests.destroy', $test) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('common.confirm_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('common.delete') }}</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('common.no_tests') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $tests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

