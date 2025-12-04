<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'test_category_id',
        'name',
        'code',
        'description',
        'price',
        'turnaround_time_hours',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category this test belongs to
     */
    public function category()
    {
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    /**
     * Get all parameters for this test
     */
    public function parameters()
    {
        return $this->hasMany(TestParameter::class)->orderBy('sort_order');
    }

    /**
     * Get active parameters only
     */
    public function activeParameters()
    {
        return $this->hasMany(TestParameter::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get all test results for this test
     */
    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }
}
