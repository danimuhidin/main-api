<?php

namespace App\Models\Motorinci;

use Illuminate\Database\Eloquent\Model;

class FeatureItem extends Model
{
    protected $table = 'motorinci_feature_items';
    protected $fillable = [
        'name',
        'desc',
        'icon',
    ];
}
