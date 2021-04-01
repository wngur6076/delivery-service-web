<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\EaterySyncException;
use App\Exceptions\OptionCountException;
use App\Http\Resources\CartItemResource;

class UserCartCartItemsController extends Controller
{
    /**
     * @OA\Post(
     *      path="/user-cart/{user_id}/cart-items",
     *      tags={"카트"},
     *      summary="카트 아이템 담기",
     *      @OA\Parameter(
     *          name="user_id",
     *          description="user_id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="menu_id",
     *          description="menu_id",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="quantity",
     *          description="수량",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="option_ids[]",
     *          description="옵션ids",
     *          in="query",
     *          @OA\Schema(type="array", @OA\Items(type="number")),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *       ),
     *       @OA\Response(
     *          response=401,
     *          description="unauthorized"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="option_count_validation_failure, eatery_sync_failure"
     *      ),
     *       security={
     *           {"bearerAuth": {}}
     *       }
     *     )
     *
     */
    public function store(User $user)
    {
        if (Auth::user()->id != $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '권환이 없습니다.',
                'error' => '401'
            ], 401);
        }

        $this->validate(request(), [
            'menu_id' => ['required'],
            'quantity' => ['required', 'numeric'],
            'option_ids' => ['nullable'],
        ]);

        $optionIds = request('option_ids') ?: [];
        $menu = Menu::findOrFail(request('menu_id'));

        try {
            $optionGroups = $menu->optionGroups;
            foreach ($optionGroups as $optionGroup) {
                $optionGroup->optionCountValidation($optionIds);
            }

            $cart = Auth::user()->getCart();
            $cart->eaterySync($menu->menuGroup->eatery_id);
            $cart->addItem(request('menu_id'), request('quantity'), $optionIds);

            return response()->json([
                'status' => 'success',
                'message' => '카트담기 성공했어요.',
            ], 200);
        } catch (OptionCountException $e) {
            return response()->json([
                'status' => 'option_count_validation_failure',
                'message' => '최소, 최대 옵션 수 확인해주세요.',
                'error' => '422',
            ], 422);
        } catch (EaterySyncException $e) {
            return response()->json([
                'status' => 'eatery_sync_failure',
                'message' => '같은 가게의 메뉴만 담을 수 있어요.',
                'error' => '422',
            ], 422);
        }
    }

    /**
     * @OA\Patch(
     *      path="/user-cart/{user_id}/cart-items/{cart_item_id}",
     *      tags={"카트"},
     *      summary="카트 아이템 업데이트",
     *      @OA\Parameter(
     *          name="user_id",
     *          description="user_id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="cart_item_id",
     *          description="cart_item_id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="quantity",
     *          description="수량",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *       ),
     *       @OA\Response(
     *          response=401,
     *          description="unauthorized"
     *      ),
     *       security={
     *           {"bearerAuth": {}}
     *       }
     *     )
     *
     */
    public function update(User $user, $id)
    {
        if (Auth::user()->id != $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '권환이 없습니다.',
                'error' => '401'
            ], 401);
        }

        $this->validate(request(), [
            'quantity' => ['required', 'numeric'],
        ]);

        $cartItem = Auth::user()->cart->items()->findOrFail($id);
        $cartItem->update(['quantity' => request('quantity')]);

        return response()->json([
            'status' => 'success',
            'message' => '카트 아이템 수량변경 성공했어요.',
            'data' => new CartItemResource($cartItem),
        ], 200);
    }

    /**
     * @OA\Delete(
     *      path="/user-cart/{user_id}/cart-items/{cart_item_id}",
     *      tags={"카트"},
     *      summary="카트 아이템 삭제",
     *      @OA\Parameter(
     *          name="user_id",
     *          description="user_id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="cart_item_id",
     *          description="cart_item_id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="success",
     *       ),
     *       @OA\Response(
     *          response=401,
     *          description="unauthorized"
     *      ),
     *       security={
     *           {"bearerAuth": {}}
     *       }
     *     )
     *
     */
    public function destroy(User $user, $id)
    {
        if (Auth::user()->id != $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '권환이 없습니다.',
                'error' => '401'
            ], 401);
        }

        $cartItem = $user->cart->items()->findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => '카트 아이템 삭제 성공했어요.',
        ], 204);
    }
}
