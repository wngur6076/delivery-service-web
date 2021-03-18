<?php

namespace App\Http\Controllers;

use App\Models\Eatery;
use Illuminate\Http\Request;
use App\Http\Resources\EateryResource;
use App\Http\Resources\MenuGroupResource;
use App\Http\Resources\MenuResource;

class EateriesController extends Controller
{
    /**
     * @OA\Get(
     *      path="/eateries/{id}",
     *      operationId="getTodoById",
     *      tags={"음식점 관련"},
     *      summary="특정 음식점 가져오기",
     *      description="특정 음식점 아이템을 가져온다.",
     *     @OA\Parameter(
     *          name="id",
     *          description="eatery_id(1~50 test-case 있음)",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="응답 성공",

     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns list of projects
     */
    public function show(Eatery $eatery)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $eatery->id,
                'title' => $eatery->title,
                'poster_image' => $eatery->poster_image_url,
                'grade' => $eatery->grade,
                'review_count' => $eatery->review_count,
                'delivery_time' => $eatery->delivery_time,
                'delivery_charge' => $eatery->delivery_charge_in_wons,
                'minimum_order_amount' => $eatery->minimum_order_amount_in_wons,
                'menu_groups' => $eatery->signatureMenus->isEmpty() ? MenuGroupResource::collection($eatery->menuGroups)
                    :  MenuGroupResource::collection($eatery->menuGroups)->prepend(['name' => '추천메뉴', 'menus' => MenuResource::collection($eatery->signatureMenus)]),
            ],
        ], 200);
    }
}
