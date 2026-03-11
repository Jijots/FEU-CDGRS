<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Added for archiving

class LostItem extends Model
{
    use SoftDeletes; // Enabled SoftDeletes trait

    // THE FIX: Explicitly tell Laravel it is safe to save the 'item_name' and 'student_id'
    protected $fillable = [
        'founder_id',
        'student_id',
        'tracking_number',
        'item_name',
        'report_type',
        'item_category',
        'description',
        'location_found',
        'date_lost',
        'image_path',
        'status',
        'is_claimed',
        'is_stock_image',
        'flagged_for_review'
    ];

    protected $casts = [
        'date_lost' => 'datetime',
    ];

    /**
     * Automatically generate a unique Tracking Number when a record is created.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tracking_number)) {
                $model->tracking_number = strtoupper(substr(uniqid('TRK-'), 0, 12));
            }
        });
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset('images/placeholder.png');
        }

        $cleanPath = str_replace('public/', '', $this->image_path);
        $cleanPath = str_replace('storage/', '', $cleanPath);

        return asset('storage/' . $cleanPath);
    }
}
