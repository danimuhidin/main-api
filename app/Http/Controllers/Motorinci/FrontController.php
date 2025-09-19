<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\Brand;
use App\Models\Motorinci\Category;
use App\Models\Motorinci\Motor;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function home($limit = 10)
    {
        $motors = Motor::inRandomOrder()->with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup'])->take($limit)->get();
        $categories = Category::with('motors')->get();
        $brands = Brand::with('motors')->get();
        return response()->json([
            'message' => 'Welcome to the Motorinci API Home',
            'status' => 'success',
            'data' => [
                'randomMotors' => $motors,
                'categories' => $categories,
                'brands' => $brands
            ]
        ]);
    }
}
