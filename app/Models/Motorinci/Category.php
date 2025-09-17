<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'motorinci_categories';
    protected $fillable = [
        'name',
        'desc',
        'image',
    ];

    public function motors()
    {
        return $this->hasMany(Motor::class, 'category_id');
    }
}
