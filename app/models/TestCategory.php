<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all tests in this category
     */
    public function tests()
    {
        return $this->hasMany(Test::class)->orderBy('sort_order');
    }

    /**
     * Get active tests only
     */
    public function activeTests()
    {
        return $this->hasMany(Test::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get all test results for tests in this category
     */
    public function testResults()
    {
        return $this->hasManyThrough(
            TestResult::class,
            Test::class,
            'test_category_id', // Foreign key on tests table
            'test_id',          // Foreign key on test_results table
            'id',               // Local key on test_categories table
            'id'                // Local key on tests table
        );
    }
}
