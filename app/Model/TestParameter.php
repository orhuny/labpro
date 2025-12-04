<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestParameter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'test_id',
        'name',
        'code',
        'unit',
        'value_type',
        'calculation_formula',
        'reference_ranges',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'reference_ranges' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the test this parameter belongs to
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Get all result values for this parameter
     */
    public function resultValues()
    {
        return $this->hasMany(TestResultValue::class);
    }

    /**
     * Get normal range for a specific gender from reference_ranges
     * Returns the first matching range or the first range if no gender match
     */
    public function getNormalRange(?string $gender = null): array
    {
        if (!$this->reference_ranges || count($this->reference_ranges) === 0) {
            return ['min' => null, 'max' => null];
        }

        // Try to find a range matching the gender label
        if ($gender) {
            $genderLabels = [
                'male' => ['erkek', 'male', 'er', 'm'],
                'female' => ['kadın', 'female', 'kad', 'f', 'folikuler', 'siklus', 'luteal', 'postmenopoz', 'menopoz'],
            ];

            $searchLabels = $genderLabels[strtolower($gender)] ?? [];
            
            foreach ($this->reference_ranges as $range) {
                $label = strtolower($range['label'] ?? '');
                foreach ($searchLabels as $searchLabel) {
                    if (stripos($label, $searchLabel) !== false) {
                        return [
                            'min' => $range['min'] ?? null,
                            'max' => $range['max'] ?? null,
                        ];
                    }
                }
            }
        }

        // Return the first range if no gender match
        $firstRange = $this->reference_ranges[0];
        return [
            'min' => $firstRange['min'] ?? null,
            'max' => $firstRange['max'] ?? null,
        ];
    }

    /**
     * Check if a value is within normal range
     * Uses reference_ranges to determine the flag
     */
    public function checkValue($value, ?string $gender = null): ?string
    {
        if ($this->value_type !== 'numeric' || !is_numeric($value)) {
            return null;
        }

        $range = $this->getNormalRange($gender);
        $numValue = (float) $value;

        // Check against the range
        if ($range['min'] !== null && $numValue < $range['min']) {
            return 'low';
        }

        if ($range['max'] !== null && $numValue > $range['max']) {
            return 'high';
        }

        return 'normal';
    }
}
