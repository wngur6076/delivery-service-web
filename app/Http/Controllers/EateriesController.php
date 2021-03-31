<?php

namespace App\Http\Controllers;

use App\Models\Eatery;
use Illuminate\Http\Request;
use App\Http\Resources\EateryResource;

class EateriesController extends Controller
{
    /**
     * @OA\Get(
     *      path="/eateries/{id}",
     *      operationId="getEateryById",
     *      tags={"음식점"},
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
            'data' => new EateryResource($eatery),
        ], 200);
    }
}
