<?php

namespace App\Http\Controllers;

use App\Models\TestResult;
use App\Models\Test;
use App\Models\Patient;
use App\Models\TestParameter;
use App\Models\TestResultValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestResultController extends Controller
{
    public function index(Request $request)
    {
        $query = TestResult::with(['patient', 'test', 'test.category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('result_id', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('patient_id', 'like', "%{$search}%");
                    });
            });
        }

        $results = $query->orderBy('created_at', 'desc')->get();
        
        // Group results by order_group_id
        $groupedResults = $results->groupBy(function ($result) {
            return $result->order_group_id ?? 'single_' . $result->id;
        });

        return view('test-results.index', compact('groupedResults'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get();
        $tests = Test::with('category')->where('is_active', true)->orderBy('name')->get();

        $selectedPatient = $request->patient_id ? Patient::find($request->patient_id) : null;
        $selectedTest = $request->test_id ? Test::with('activeParameters')->find($request->test_id) : null;

        return view('test-results.create', compact('patients', 'tests', 'selectedPatient', 'selectedTest'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'exists:tests,id',
            'order_date' => 'required|date',
            'sample_collection_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $createdResults = [];
        $errors = [];
        
        // Generate a unique order group ID for this batch of tests
        $orderGroupId = \Illuminate\Support\Str::uuid()->toString();

        // Create a test result for each selected test
        foreach ($validated['test_ids'] as $testId) {
            $maxRetries = 20;
            $testResult = null;
            
            for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
                try {
                    $testResult = DB::transaction(function () use ($validated, $testId, $attempt, $orderGroupId) {
                        $data = [
                            'patient_id' => $validated['patient_id'],
                            'test_id' => $testId,
                            'order_date' => $validated['order_date'],
                            'sample_collection_date' => $validated['sample_collection_date'] ?? null,
                            'notes' => $validated['notes'] ?? null,
                            'result_id' => TestResult::generateResultId($attempt),
                            'order_group_id' => $orderGroupId,
                            'ordered_by' => auth()->id(),
                            'status' => 'pending',
                        ];

                        return TestResult::create($data);
                    }, 5); // 5 second timeout
                    
                    // Success, break out of loop
                    break;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if it's a duplicate entry error (error code 23000)
                    $errorCode = $e->getCode();
                    $errorMessage = $e->getMessage();
                    
                    $isDuplicate = ($errorCode == 23000 || $errorCode == '23000') && (
                        stripos($errorMessage, 'Duplicate entry') !== false || 
                        stripos($errorMessage, '1062') !== false ||
                        stripos($errorMessage, 'test_results_result_id_unique') !== false ||
                        stripos($errorMessage, 'unique') !== false
                    );
                    
                    if ($isDuplicate && $attempt < $maxRetries - 1) {
                        // Wait a random amount of time to avoid thundering herd
                        usleep(rand(50000, 200000)); // 50-200ms
                        continue; // Retry with new ID (offset will be different)
                    }
                    
                    // If it's not a duplicate or we've exhausted retries, save error and continue
                    $errors[] = "Failed to create test result for test ID {$testId}: " . $e->getMessage();
                    break;
                } catch (\Exception $e) {
                    // For any other exception, save error and continue
                    $errors[] = "Failed to create test result for test ID {$testId}: " . $e->getMessage();
                    break;
                }
            }
            
            if ($testResult) {
                $createdResults[] = $testResult;
            }
        }

        // If no results were created, return with error
        if (empty($createdResults)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create test results. ' . implode(' ', $errors));
        }

        // If some results were created but not all, show warning
        if (count($createdResults) < count($validated['test_ids'])) {
            return redirect()->route('test-results.index')
                ->with('warning', count($createdResults) . ' test result(s) created successfully, but some failed: ' . implode(' ', $errors));
        }

        // If only one result was created, redirect to it
        if (count($createdResults) === 1) {
            return redirect()->route('test-results.show', $createdResults[0])
                ->with('success', __('common.test_result_created_successfully_enter_values'));
        }

        // If multiple results were created, redirect to index
        return redirect()->route('test-results.index')
            ->with('success', count($createdResults) . ' ' . __('common.test_results_created_successfully'));
    }

    public function show(TestResult $testResult)
    {
        $testResult->load([
            'patient',
            'test.category',
            'test.activeParameters',
            'values.parameter',
            'orderedBy',
            'performedBy'
        ]);

        return view('test-results.show', compact('testResult'));
    }

    public function edit(TestResult $testResult)
    {
        if ($testResult->status === 'completed' && !auth()->user()->isAdmin()) {
            abort(403, 'Cannot edit completed test results.');
        }

        $testResult->load(['test.activeParameters', 'values.parameter']);
        return view('test-results.edit', compact('testResult'));
    }

    public function update(Request $request, TestResult $testResult)
    {
        if ($testResult->status === 'completed' && !auth()->user()->isAdmin()) {
            abort(403, 'Cannot edit completed test results.');
        }

        $validated = $request->validate([
            'sample_collection_date' => 'nullable|date',
            'result_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'doctor_remarks' => 'nullable|string',
            'technician_notes' => 'nullable|string',
            'values' => 'nullable|array',
            'values.*.parameter_id' => 'required|exists:test_parameters,id',
            'values.*.value' => 'nullable|string',
            'values.*.is_outside_normal_range' => 'nullable|boolean',
            'values.*.notes' => 'nullable|string',
        ]);

        // Update basic fields
        $testResult->update([
            'sample_collection_date' => $validated['sample_collection_date'] ?? $testResult->sample_collection_date,
            'result_date' => $validated['result_date'] ?? $testResult->result_date,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $testResult->notes,
            'doctor_remarks' => $validated['doctor_remarks'] ?? $testResult->doctor_remarks,
            'technician_notes' => $validated['technician_notes'] ?? $testResult->technician_notes,
            'performed_by' => ($validated['status'] === 'completed' || $validated['status'] === 'in_progress') 
                ? auth()->id() 
                : $testResult->performed_by,
        ]);

        // Update or create result values
        if (isset($validated['values'])) {
            foreach ($validated['values'] as $valueData) {
                $parameter = TestParameter::find($valueData['parameter_id']);
                if (!$parameter) continue;

                $value = $valueData['value'] ?? null;
                $isOutsideNormalRange = isset($valueData['is_outside_normal_range']) && $valueData['is_outside_normal_range'] == '1';

                TestResultValue::updateOrCreate(
                    [
                        'test_result_id' => $testResult->id,
                        'test_parameter_id' => $parameter->id,
                    ],
                    [
                        'value' => $value,
                        'is_outside_normal_range' => $isOutsideNormalRange,
                        'notes' => $valueData['notes'] ?? null,
                    ]
                );
            }
        }

        // Check if result is abnormal
        $testResult->checkAbnormal();

        return redirect()->route('test-results.show', $testResult)
            ->with('success', 'Test result updated successfully.');
    }

    public function destroy(TestResult $testResult)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete test results.');
        }

        $testResult->delete();

        return redirect()->route('test-results.index')
            ->with('success', 'Test result deleted successfully.');
    }
}
