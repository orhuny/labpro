<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResultValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_result_id',
        'test_parameter_id',
        'value',
        'is_outside_normal_range',
        'notes',
    ];

    protected $casts = [
        'is_outside_normal_range' => 'boolean',
    ];

    /**
     * Get the test result this value belongs to
     */
    public function testResult()
    {
        return $this->belongsTo(TestResult::class);
    }

    /**
     * Get the parameter this value is for
     */
    public function parameter()
    {
        return $this->belongsTo(TestParameter::class, 'test_parameter_id');
    }
}
