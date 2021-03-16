<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCategoryResource;
use App\Models\Eatery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EateriesController extends Controller
{
    public function show(Eatery $eatery)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $eatery->id,
                'title' => $eatery->title,
                'poster_image' => Storage::disk('public')->url($eatery->poster_image_path),
                'grade' => 2.1,
                'review_count' => 26,
                'delivery_time' => $eatery->delivery_time,
                'delivery_charge' => number_format($eatery->delivery_charge),
                'minimum_order_amount' => number_format($eatery->minimum_order_amount),
                'menu_category' => MenuCategoryResource::collection($eatery->menuCategories),
            ],
        ], 200);
    }
}
