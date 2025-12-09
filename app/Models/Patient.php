<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'name',
        'date_of_birth',
        'age',
        'gender',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'doctor_name',
        'doctor_referral',
        'medical_history',
        'allergies',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get all test results for this patient
     */
    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    /**
     * Generate unique patient ID
     */
    public static function generatePatientId(): string
    {
        $prefix = 'PAT';

        return DB::transaction(function () use ($prefix) {
            // Lock the table to prevent race conditions
            $last = DB::table('patients')
                ->select('patient_id')
                ->where('patient_id', 'like', $prefix . '%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $lastNumber = 0;
            if ($last && isset($last->patient_id)) {
                $lastNumber = (int) substr($last->patient_id, strlen($prefix));
            }

            // Increment and format
            $nextNumber = $lastNumber + 1;
            $nextId = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Ensure unique (unlikely needed with lock, but double-check)
            $attempts = 0;
            while (self::where('patient_id', $nextId)->exists()) {
                $attempts++;
                $nextNumber++;
                $nextId = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                if ($attempts > 5) {
                    throw new \RuntimeException('Unable to generate unique patient_id');
                }
            }

            return $nextId;
        });
    }
}
