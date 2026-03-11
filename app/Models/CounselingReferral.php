<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CounselingReferral extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'referrer_id',
        'case_reference',
        'support_urgency',
        'administrative_status',
        'administrative_observations',
        'scheduled_date',
        'guidance_notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    /**
     * Relationship: The student receiving counseling support.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relationship: The administrator who created this referral.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }
}
