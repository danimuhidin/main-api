<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorImage extends Model
{
    protected $table = 'motorinci_motor_images';
    protected $fillable = [
        'motor_id',
        'image',
        'desc',
        'order',
    ];
    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }
}
