<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestParameter;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $query = Test::with('category');

        if ($request->has('category_id')) {
            $query->where('test_category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $tests = $query->orderBy('sort_order')->orderBy('name')->paginate(15);
        $categories = TestCategory::where('is_active', true)->orderBy('name')->get();

        return view('tests.index', compact('tests', 'categories'));
    }

    public function create()
    {
        $categories = TestCategory::where('is_active', true)->orderBy('name')->get();
        return view('tests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'test_category_id' => 'required|exists:test_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tests,code',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'turnaround_time_hours' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Test::create($validated);

        return redirect()->route('tests.index')
            ->with('success', 'Test created successfully.');
    }

    public function show(Test $test)
    {
        $test->load('category', 'parameters');
        return view('tests.show', compact('test'));
    }

    public function edit(Test $test)
    {
        $categories = TestCategory::where('is_active', true)->orderBy('name')->get();
        return view('tests.edit', compact('test', 'categories'));
    }

    public function update(Request $request, Test $test)
    {
        $validated = $request->validate([
            'test_category_id' => 'required|exists:test_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tests,code,' . $test->id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'turnaround_time_hours' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $test->update($validated);

        return redirect()->route('tests.index')
            ->with('success', 'Test updated successfully.');
    }

    public function destroy(Test $test)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete tests.');
        }

        $test->delete();

        return redirect()->route('tests.index')
            ->with('success', 'Test deleted successfully.');
    }

    public function parameters(Test $test)
    {
        $test->load(['parameters.resultValues']);
        return view('tests.parameters', compact('test'));
    }

    public function storeParameter(Request $request, Test $test)
    {
        // Clean up reference_ranges before validation: convert empty strings to null and filter out empty ranges
        $data = $request->all();
        if (isset($data['reference_ranges']) && is_array($data['reference_ranges'])) {
            $cleanedRanges = [];
            foreach ($data['reference_ranges'] as $range) {
                // Clean up empty strings
                if (isset($range['min']) && ($range['min'] === '' || $range['min'] === null)) {
                    $range['min'] = null;
                } else if (isset($range['min'])) {
                    $range['min'] = is_numeric($range['min']) ? (float)$range['min'] : null;
                }
                if (isset($range['max']) && ($range['max'] === '' || $range['max'] === null)) {
                    $range['max'] = null;
                } else if (isset($range['max'])) {
                    $range['max'] = is_numeric($range['max']) ? (float)$range['max'] : null;
                }
                if (isset($range['value']) && ($range['value'] === '' || $range['value'] === null)) {
                    $range['value'] = null;
                }
                if (isset($range['prefix']) && ($range['prefix'] === '' || $range['prefix'] === null)) {
                    $range['prefix'] = null;
                }
                
                // Only keep ranges that have at least a label
                $hasLabel = !empty($range['label']);
                
                if ($hasLabel) {
                    $cleanedRanges[] = $range;
                }
            }
            
            // If no valid ranges remain, set to null, otherwise use cleaned array
            $data['reference_ranges'] = empty($cleanedRanges) ? null : $cleanedRanges;
            $request->merge($data);
        } else {
            // If reference_ranges is not set or empty, set to null
            $data['reference_ranges'] = null;
            $request->merge($data);
        }

        // Build validation rules dynamically
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'value_type' => 'required|in:numeric,text,boolean,calculated',
            'calculation_formula' => 'nullable|string',
            'reference_ranges' => 'nullable|array',
            'reference_html' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
        
        // Only add reference_ranges validation if there are ranges
        if (isset($data['reference_ranges']) && is_array($data['reference_ranges']) && !empty($data['reference_ranges'])) {
            $rules['reference_ranges.*.label'] = 'required|string|max:255';
            $rules['reference_ranges.*.min'] = 'nullable|numeric';
            $rules['reference_ranges.*.max'] = 'nullable|numeric';
            $rules['reference_ranges.*.value'] = 'nullable|string|max:50';
            $rules['reference_ranges.*.prefix'] = 'nullable|string|max:10';
        }

        $validated = $request->validate($rules);

        $test->parameters()->create($validated);

        return redirect()->route('tests.parameters', $test)
            ->with('success', 'Parameter added successfully.');
    }

    public function updateParameter(Request $request, Test $test, TestParameter $parameter)
    {
        // Clean up reference_ranges before validation: convert empty strings to null and filter out empty ranges
        $data = $request->all();
        if (isset($data['reference_ranges']) && is_array($data['reference_ranges'])) {
            $cleanedRanges = [];
            foreach ($data['reference_ranges'] as $range) {
                // Clean up empty strings
                if (isset($range['min']) && ($range['min'] === '' || $range['min'] === null)) {
                    $range['min'] = null;
                } else if (isset($range['min'])) {
                    $range['min'] = is_numeric($range['min']) ? (float)$range['min'] : null;
                }
                if (isset($range['max']) && ($range['max'] === '' || $range['max'] === null)) {
                    $range['max'] = null;
                } else if (isset($range['max'])) {
                    $range['max'] = is_numeric($range['max']) ? (float)$range['max'] : null;
                }
                if (isset($range['value']) && ($range['value'] === '' || $range['value'] === null)) {
                    $range['value'] = null;
                }
                if (isset($range['prefix']) && ($range['prefix'] === '' || $range['prefix'] === null)) {
                    $range['prefix'] = null;
                }
                
                // Only keep ranges that have at least a label
                $hasLabel = !empty($range['label']);
                
                if ($hasLabel) {
                    $cleanedRanges[] = $range;
                }
            }
            
            // If no valid ranges remain, set to null, otherwise use cleaned array
            $data['reference_ranges'] = empty($cleanedRanges) ? null : $cleanedRanges;
            $request->merge($data);
        } else {
            // If reference_ranges is not set or empty, set to null
            $data['reference_ranges'] = null;
            $request->merge($data);
        }

        // Build validation rules dynamically
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'value_type' => 'required|in:numeric,text,boolean,calculated',
            'calculation_formula' => 'nullable|string',
            'reference_ranges' => 'nullable|array',
            'reference_html' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
        
        // Only add reference_ranges validation if there are ranges
        if (isset($data['reference_ranges']) && is_array($data['reference_ranges']) && !empty($data['reference_ranges'])) {
            $rules['reference_ranges.*.label'] = 'required|string|max:255';
            $rules['reference_ranges.*.min'] = 'nullable|numeric';
            $rules['reference_ranges.*.max'] = 'nullable|numeric';
            $rules['reference_ranges.*.value'] = 'nullable|string|max:50';
            $rules['reference_ranges.*.prefix'] = 'nullable|string|max:10';
        }

        $validated = $request->validate($rules);

        $parameter->update($validated);

        return redirect()->route('tests.parameters', $test)
            ->with('success', 'Parameter updated successfully.');
    }

    public function destroyParameter(Test $test, TestParameter $parameter)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete parameters.');
        }

        // Check if parameter is already assigned to any test results
        $resultCount = $parameter->resultValues()->count();
        
        if ($resultCount > 0) {
            return redirect()->route('tests.parameters', $test)
                ->with('error', "Cannot delete parameter '{$parameter->name}'. It is already assigned to {$resultCount} test result(s). Please remove all assignments before deleting.");
        }

        $parameter->delete();

        return redirect()->route('tests.parameters', $test)
            ->with('success', 'Parameter deleted successfully.');
    }
}
