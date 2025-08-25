<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailableColor extends Model
{
    protected $table = 'motorinci_available_colors';
    protected $fillable = [
        'motor_id',
        'color_id',
        'image',
    ];
    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }

    /**
     * Relasi ke model MotorinciColor.
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
