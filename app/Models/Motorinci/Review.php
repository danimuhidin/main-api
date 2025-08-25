<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'motorinci_reviews';
    protected $fillable = [
        'motor_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function motor()
    {
        // return $this->belongsTo(Motor::class, 'motor_id');
    }
}
