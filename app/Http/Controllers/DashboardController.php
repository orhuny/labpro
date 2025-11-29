<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\TestResult;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'total_tests' => Test::count(),
            'total_categories' => TestCategory::count(),
            'today_results' => TestResult::whereDate('order_date', today())->count(),
            'pending_results' => TestResult::where('status', 'pending')->count(),
            'completed_today' => TestResult::where('status', 'completed')
                ->whereDate('result_date', today())->count(),
            'abnormal_results' => TestResult::where('is_abnormal', true)
                ->whereDate('result_date', '>=', now()->subDays(7))->count(),
        ];

        // Recent test results
        $recentResults = TestResult::with(['patient', 'test'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Daily statistics for the last 7 days
        $dailyStats = TestResult::select(
            DB::raw('DATE(order_date) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('order_date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Test category distribution
        $categoryStats = TestCategory::withCount('testResults')
            ->orderBy('test_results_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentResults', 'dailyStats', 'categoryStats'));
    }
}
