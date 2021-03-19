<?php

namespace App\Http\Controllers;

use App\Http\Resources\OptionGroupResource;
use App\Models\Menu;
use App\Models\Eatery;
use Illuminate\Http\Request;

class EateriesMenusController extends Controller
{
    public function show(Eatery $eatery, Menu $menu)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => number_format($menu->price),
                'description' => $menu->description,
                'image' => $menu->image_url,
                'option_groups' => OptionGroupResource::collection($menu->optionGroups),
            ],
        ], 200);
    }
}
