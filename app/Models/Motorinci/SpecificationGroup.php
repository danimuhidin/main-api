<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;

class SpecificationGroup extends Model
{
    protected $table = 'motorinci_specification_groups';
    protected $fillable = [
        'name',
        'order',
    ];
}
