<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'motorinci_brands';
    protected $fillable = [
        'name',
        'desc',
        'icon',
        'image',
    ];
    protected $guarded = [
        'id',
    ];

    public function motors()
    {
        return $this->hasMany(Motor::class, 'brand_id')
        ->with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup', 'reviews', 'availableColors.color'])
        ->orderBy('name', 'ASC')
        ->orderBy('year_model', 'DESC');
    }
}
