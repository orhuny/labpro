<?php

namespace App\Http\Controllers;

use App\Models\TestResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function generate(TestResult $testResult)
    {
        try {
            // Get all test results in the same order group
            $testResults = TestResult::where('order_group_id', $testResult->order_group_id)
                ->where('patient_id', $testResult->patient_id)
                ->with([
                    'patient',
                    'test.category',
                    'test.activeParameters',
                    'values.parameter',
                    'orderedBy',
                    'performedBy'
                ])
                ->join('tests', 'test_results.test_id', '=', 'tests.id')
                ->join('test_categories', 'tests.test_category_id', '=', 'test_categories.id')
                ->orderBy('test_categories.sort_order')
                ->orderBy('tests.sort_order')
                ->select('test_results.*')
                ->get();

            // If no group, just use the single test result
            if ($testResults->isEmpty()) {
                $testResult->load([
                    'patient',
                    'test.category',
                    'test.activeParameters',
                    'values.parameter',
                    'orderedBy',
                    'performedBy'
                ]);
                $testResults = collect([$testResult]);
            }

            // Sort values by parameter sort_order for each test result
            foreach ($testResults as $result) {
                $result->setRelation('values', $result->values->sortBy(function ($value) {
                    return $value->parameter ? $value->parameter->sort_order : 999;
                })->values());
            }

            // Prepare logo as base64 if exists
            $logoBase64 = null;
            $logoPathForView = null;
            
            // Try different logo filenames (logo.png has priority)
            $possibleLogos = ['logo.png', 'yamanlab.png'];
            foreach ($possibleLogos as $logoFile) {
                try {
                    $logoPath = public_path('images/' . $logoFile);
                    if (file_exists($logoPath)) {
                        $logoData = file_get_contents($logoPath);
                        if ($logoData !== false) {
                            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                            $logoPathForView = $logoPath;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    // Continue to next logo if this one fails
                    continue;
                }
            }

            $data = [
                'testResults' => $testResults,
                'testResult' => $testResult, // Keep for backward compatibility
                'labName' => config('app.name', 'Laboratory Management System'),
                'labAddress' => config('app.lab_address', ''),
                'labPhone' => config('app.lab_phone', ''),
                'labEmail' => config('app.lab_email', ''),
                'logoBase64' => $logoBase64,
                'logoPath' => $logoPathForView,
            ];

            $pdf = Pdf::loadView('reports.test-result', $data);
            
            // Set encoding and options for Turkish characters
            $pdf->setOption('encoding', 'UTF-8');
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', false);
            
            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');
            
            $fileName = $testResults->count() > 1 
                ? 'test-results-group-' . $testResult->order_group_id . '.pdf'
                : 'test-result-' . $testResult->result_id . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('PDF generation failed', [
                'test_result_id' => $testResult->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Redirect back with error message
            return redirect()->back()
                ->with('error', 'PDF oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    public function view(TestResult $testResult)
    {
        try {
            // Get all test results in the same order group
            $testResults = TestResult::where('order_group_id', $testResult->order_group_id)
                ->where('patient_id', $testResult->patient_id)
                ->with([
                    'patient',
                    'test.category',
                    'test.activeParameters',
                    'values.parameter',
                    'orderedBy',
                    'performedBy'
                ])
                ->join('tests', 'test_results.test_id', '=', 'tests.id')
                ->join('test_categories', 'tests.test_category_id', '=', 'test_categories.id')
                ->orderBy('test_categories.sort_order')
                ->orderBy('tests.sort_order')
                ->select('test_results.*')
                ->get();

            // If no group, just use the single test result
            if ($testResults->isEmpty()) {
                $testResult->load([
                    'patient',
                    'test.category',
                    'test.activeParameters',
                    'values.parameter',
                    'orderedBy',
                    'performedBy'
                ]);
                $testResults = collect([$testResult]);
            }

            // Sort values by parameter sort_order for each test result
            foreach ($testResults as $result) {
                $result->setRelation('values', $result->values->sortBy(function ($value) {
                    return $value->parameter ? $value->parameter->sort_order : 999;
                })->values());
            }

            // Prepare logo as base64 if exists
            $logoBase64 = null;
            $logoPathForView = null;
            
            // Try different logo filenames (logo.png has priority)
            $possibleLogos = ['logo.png', 'yamanlab.png'];
            foreach ($possibleLogos as $logoFile) {
                try {
                    $logoPath = public_path('images/' . $logoFile);
                    if (file_exists($logoPath)) {
                        $logoData = file_get_contents($logoPath);
                        if ($logoData !== false) {
                            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                            $logoPathForView = $logoPath;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    // Continue to next logo if this one fails
                    continue;
                }
            }

            return view('reports.test-result', [
                'testResults' => $testResults,
                'testResult' => $testResult, // Keep for backward compatibility
                'labName' => config('app.name', 'Laboratory Management System'),
                'labAddress' => config('app.lab_address', ''),
                'labPhone' => config('app.lab_phone', ''),
                'labEmail' => config('app.lab_email', ''),
                'logoBase64' => $logoBase64,
                'logoPath' => $logoPathForView,
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('PDF view failed', [
                'test_result_id' => $testResult->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Redirect back with error message
            return redirect()->back()
                ->with('error', 'Rapor görüntülenirken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }
}
