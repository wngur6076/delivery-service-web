<?php

namespace App\Http\Controllers;

use App\Http\Resources\EateryResource;
use App\Http\Resources\MenuCategoryResource;
use App\Models\Eatery;
use App\Models\Review;
use Illuminate\Http\Request;

class EateriesController extends Controller
{
    public function show(Eatery $eatery)
    {
        return response()->json([
            'status' => 'success',
            'data' => new EateryResource($eatery),
        ], 200);
    }
}
