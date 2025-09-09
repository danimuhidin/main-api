<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;
    protected $table = 'motorinci_colors';
    protected $fillable = [
        'name',
        'hex',
    ];
    protected $guarded = [
        'id',
    ];
}
