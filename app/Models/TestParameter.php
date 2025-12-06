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

    /**
     * Check if a value is outside normal range based on reference_ranges
     * Returns true if value is outside normal range, false otherwise
     * 
     * @param mixed $value The value to check
     * @param string|null $gender Patient gender for gender-specific ranges
     * @return bool True if outside normal range, false if within normal range
     */
    public function isValueOutsideNormalRange($value, ?string $gender = null): bool
    {
        // If value is empty or not numeric, cannot determine
        if (empty($value) || !is_numeric($value)) {
            return false;
        }

        if (!$this->reference_ranges || count($this->reference_ranges) === 0) {
            return false;
        }

        $numValue = (float) $value;
        $applicableRange = null;

        // Try to find a range matching the gender
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
                        $applicableRange = $range;
                        break 2;
                    }
                }
            }
        }

        // If no gender match, use the first range
        if (!$applicableRange) {
            $applicableRange = $this->reference_ranges[0];
        }

        $min = $applicableRange['min'] ?? null;
        $max = $applicableRange['max'] ?? null;

        // If both min and max are null, cannot determine
        if ($min === null && $max === null) {
            return false;
        }

        // Check if value is outside the range
        // If min exists: value must be >= min
        if ($min !== null && $min !== '' && $numValue < (float)$min) {
            return true;
        }

        // If max exists: value must be <= max
        if ($max !== null && $max !== '' && $numValue > (float)$max) {
            return true;
        }

        // Value is within normal range
        return false;
    }
}
