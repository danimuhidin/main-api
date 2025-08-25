<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorFeature extends Model
{
    protected $table = 'motorinci_motor_feature';
    protected $fillable = [
        'motor_id',
        'feature_item_id',
    ];
    
    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }

    public function featureItem(): BelongsTo
    {
        return $this->belongsTo(FeatureItem::class, 'feature_item_id');
    }
}
