<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCartsController extends Controller
{
    public function destroy(User $user)
    {
        if (Auth::user()->id != $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '권환이 없습니다.',
                'error' => '401'
            ], 401);
        }

        $user->cart()->delete();

        return response()->json([
            'status' => 'success',
            'message' => '카트 삭제 성공했어요.',
        ], 204);
    }
}
