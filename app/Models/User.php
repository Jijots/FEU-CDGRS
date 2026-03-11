<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'id_number',
        'role',
        'program_code',
        'campus',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship: Disciplinary violations assigned to this student.
     */
    public function violations()
    {
        return $this->hasMany(Violation::class, 'student_id');
    }

    /**
     * NEW: Relationship for Incident Reports.
     * This fixes the "Call to undefined method incidentReports()" error.
     */
    public function incidentReports()
    {
        // Using 'student_id' to match your violations and lostItems structure
        return $this->hasMany(IncidentReport::class, 'student_id');
    }

    /**
     * Items found by this user (intended for staff/guards).
     */
    public function foundItems()
    {
        return $this->hasMany(LostItem::class, 'founder_id');
    }

    /**
     * Items lost by this user.
     */
    public function lostItems()
    {
        return $this->hasMany(LostItem::class, 'student_id');
    }

    /**
     * Counseling referrals for this student.
     */
    public function counselingReferralsAsStudent()
    {
        return $this->hasMany(CounselingReferral::class, 'student_id');
    }

    /**
     * Counseling referrals created by this administrator.
     */
    public function counselingReferralsAsReferrer()
    {
        return $this->hasMany(CounselingReferral::class, 'referrer_id');
    }
}
