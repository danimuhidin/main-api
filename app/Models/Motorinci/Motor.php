<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    protected $table = 'motorinci_motors';
    protected $fillable = [
        'name',
        'brand_id',
        'category_id',
        'year_model',
        'engine_cc',
        'low_price',
        'up_price',
        'desc',
        'brochure_url',
        'sparepart_url',
        'is_active',
        'is_featured',
        'published_at',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function features()
    {
        return $this->hasMany(MotorFeature::class, 'motor_id');
    }

    public function images()
    {
        return $this->hasMany(MotorImage::class, 'motor_id');
    }

    public function specifications()
    {
        return $this->hasMany(MotorSpecification::class, 'motor_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'motor_id');
    }

    public function availableColors()
    {
        return $this->hasMany(AvailableColor::class, 'motor_id');
    }
}
