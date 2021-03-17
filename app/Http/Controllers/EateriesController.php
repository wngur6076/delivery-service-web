<?php

namespace App\Http\Controllers;

use App\Http\Resources\EateryResource;
use App\Models\Eatery;
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
