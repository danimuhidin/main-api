<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorSpecification extends Model
{
    protected $table = 'motorinci_motor_specifications';
    protected $fillable = [
        'motor_id',
        'specification_item_id',
        'value',
    ];
    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }

    public function specificationItem(): BelongsTo
    {
        return $this->belongsTo(SpecificationItem::class, 'specification_item_id');
    }
}
