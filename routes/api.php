<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes (Protected by Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Patients API
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);
    Route::get('/patients/{patient}/test-results', [PatientController::class, 'testResults']);
    
    // Test Categories API
    Route::get('/test-categories', [TestCategoryController::class, 'index']);
    Route::post('/test-categories', [TestCategoryController::class, 'store']);
    Route::get('/test-categories/{testCategory}', [TestCategoryController::class, 'show']);
    Route::put('/test-categories/{testCategory}', [TestCategoryController::class, 'update']);
    Route::delete('/test-categories/{testCategory}', [TestCategoryController::class, 'destroy']);
    
    // Tests API
    Route::get('/tests', [TestController::class, 'index']);
    Route::post('/tests', [TestController::class, 'store']);
    Route::get('/tests/{test}', [TestController::class, 'show']);
    Route::put('/tests/{test}', [TestController::class, 'update']);
    Route::delete('/tests/{test}', [TestController::class, 'destroy']);
    Route::get('/tests/{test}/parameters', [TestController::class, 'parameters']);
    
    // Test Results API
    Route::get('/test-results', [TestResultController::class, 'index']);
    Route::post('/test-results', [TestResultController::class, 'store']);
    Route::get('/test-results/{testResult}', [TestResultController::class, 'show']);
    Route::put('/test-results/{testResult}', [TestResultController::class, 'update']);
    Route::delete('/test-results/{testResult}', [TestResultController::class, 'destroy']);
    
    // Reports API
    Route::get('/reports/{testResult}/generate', [ReportController::class, 'generate']);
});
