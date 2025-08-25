<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;

class SpecificationItem extends Model
{
    protected $table = 'motorinci_specification_items';
    protected $fillable = [
        'specification_group_id',
        'name',
        'unit',
        'desc',
        'order',
    ];
    public function specificationGroup()
    {
        return $this->belongsTo(SpecificationGroup::class, 'specification_group_id');
    }
}
