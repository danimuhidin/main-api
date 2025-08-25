<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'motorinci_colors';
    protected $fillable = [
        'name',
        'hex',
    ];
}
