<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Language Switch
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('language.switch');

// Authentication Routes (manual implementation)
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Dashboard (protected routes)
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Patients
    Route::resource('patients', PatientController::class);
    
    // Test Categories
    Route::resource('test-categories', TestCategoryController::class);
    
    // Tests
    Route::resource('tests', TestController::class);
    Route::get('tests/{test}/parameters', [TestController::class, 'parameters'])->name('tests.parameters');
    Route::post('tests/{test}/parameters', [TestController::class, 'storeParameter'])->name('tests.parameters.store');
    Route::put('tests/{test}/parameters/{parameter}', [TestController::class, 'updateParameter'])->name('tests.parameters.update');
    Route::delete('tests/{test}/parameters/{parameter}', [TestController::class, 'destroyParameter'])->name('tests.parameters.destroy');
    
    // Test Results
    Route::resource('test-results', TestResultController::class);
    
    // Reports
    Route::get('reports/{testResult}/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{testResult}/view', [ReportController::class, 'view'])->name('reports.view');
});

// Admin only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // User Management
    Route::resource('users', App\Http\Controllers\UserController::class);
});
