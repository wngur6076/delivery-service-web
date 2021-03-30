<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RefreshController extends Controller
{
    public function store()
    {
        if (! $token = auth()->guard()->refresh()) {
            return response()->json([
                'status' => 'refresh_token_error',
                'message' => '토큰 재발급 실패했습니다.',
                'error' => '401'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => '토큰 재발급 성공했습니다.',
        ], 200)->header('Authorization', $token);
    }
}
