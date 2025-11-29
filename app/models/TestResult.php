<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TestResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'result_id',
        'order_group_id',
        'patient_id',
        'test_id',
        'ordered_by',
        'performed_by',
        'order_date',
        'sample_collection_date',
        'result_date',
        'status',
        'notes',
        'doctor_remarks',
        'technician_notes',
        'is_abnormal',
    ];

    protected $casts = [
        'order_date' => 'date',
        'sample_collection_date' => 'date',
        'result_date' => 'date',
        'is_abnormal' => 'boolean',
    ];

    /**
     * Get the patient for this test result
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the test for this result
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Get the user who ordered this test
     */
    public function orderedBy()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    /**
     * Get the user who performed this test
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get all result values for this test result
     */
    public function values()
    {
        return $this->hasMany(TestResultValue::class);
    }

    /**
     * Generate unique result ID
     * Must be called within a transaction to prevent race conditions
     * 
     * @param int $retryOffset Additional offset to add when retrying
     */
    public static function generateResultId(int $retryOffset = 0): string
    {
        $prefix = 'RES';
        $date = date('Ymd');
        
        // Get all existing IDs for today with lock
        // Use a subquery to get max number more reliably
        $maxNumber = DB::table('test_results')
            ->where('result_id', 'like', $prefix . $date . '%')
            ->lockForUpdate()
            ->get()
            ->map(function ($row) {
                // Extract number from result_id (last 4 characters)
                return (int) substr($row->result_id, -4);
            })
            ->max();
        
        $maxNumber = $maxNumber ?? 0;
        
        // Start from the next number + retry offset
        $number = $maxNumber + 1 + $retryOffset;
        
        // Generate the new result_id and ensure it's unique
        $maxAttempts = 200;
        $attempt = 0;
        
        do {
            $newResultId = $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
            
            // Check if it exists with lock
            $exists = self::where('result_id', $newResultId)
                ->lockForUpdate()
                ->exists();
            
            if (!$exists) {
                return $newResultId;
            }
            
            $number++;
            $attempt++;
        } while ($attempt < $maxAttempts);
        
        throw new \Exception('Unable to generate unique result ID after ' . $maxAttempts . ' attempts.');
    }

    /**
     * Mark result as abnormal if any value is outside normal range
     */
    public function checkAbnormal(): void
    {
        $hasAbnormal = $this->values()
            ->where('is_outside_normal_range', true)
            ->exists();

        $this->is_abnormal = $hasAbnormal;
        $this->save();
    }
}
