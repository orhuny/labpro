<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestCategoryController extends Controller
{
    public function index()
    {
        $categories = TestCategory::withCount('tests')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('test-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('test-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:test_categories,code',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        TestCategory::create($validated);

        return redirect()->route('test-categories.index')
            ->with('success', 'Test category created successfully.');
    }

    public function show(TestCategory $testCategory)
    {
        $testCategory->load('tests.parameters');
        return view('test-categories.show', compact('testCategory'));
    }

    public function edit(TestCategory $testCategory)
    {
        return view('test-categories.edit', compact('testCategory'));
    }

    public function update(Request $request, TestCategory $testCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:test_categories,code,' . $testCategory->id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $testCategory->update($validated);

        return redirect()->route('test-categories.index')
            ->with('success', 'Test category updated successfully.');
    }

    public function destroy(TestCategory $testCategory)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete test categories.');
        }

        $testCategory->delete();

        return redirect()->route('test-categories.index')
            ->with('success', 'Test category deleted successfully.');
    }
}
