<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Resources\OptionGroupResource;

class MenusController extends Controller
{
    /**
     * @OA\Get(
     *      path="/menus/{id}",
     *      operationId="getMenuById",
     *      tags={"메뉴 관련"},
     *      summary="특정 메뉴 가져오기",
     *      description="특정 메뉴 아이템을 가져온다.",
     *     @OA\Parameter(
     *          name="id",
     *          description="menu_id(메뉴아이디 적어주세요.)",
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
    public function show(Menu $menu)
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
