<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


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
        $lastPatient = self::orderBy('id', 'desc')->first();
        $number = $lastPatient ? ((int) substr($lastPatient->patient_id, 3)) + 1 : 1;
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
