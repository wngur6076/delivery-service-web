<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\EaterySyncException;
use App\Exceptions\OptionCountException;
use App\Models\Menu;

class CartController extends Controller
{
    public function store($eateryId)
    {
        try {
            $optionIds = request('option_ids') ?: [];

            $optionGroups = Menu::findOrFail(request('menu_id'))->optionGroups;
            foreach ($optionGroups as $optionGroup) {
                $optionGroup->optionCountValidation($optionIds);
            }

            $cart = Auth::user()->getCart($eateryId);
            $cart->addItem(request('menu_id'), request('quantity'), $optionIds);

            return response()->json([], 200);
        } catch (OptionCountException $e) {
            return response()->json([], 422);
        } catch (EaterySyncException $e) {
            return response()->json([], 422);
        }
    }
}
