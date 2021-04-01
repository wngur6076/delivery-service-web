<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuDetailsResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenusController extends Controller
{
    /**
     * @OA\Get(
     *      path="/menus/{menu_id}",
     *      tags={"메뉴"},
     *      summary="특정 메뉴 가져오기",
     *      description="특정 메뉴 아이템을 가져온다.",
     *      @OA\Parameter(
     *          name="menu_id",
     *          description="menu_id",
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
    public function show($id)
    {
        $menu = Menu::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new MenuDetailsResource($menu),
        ], 200);
    }
}
