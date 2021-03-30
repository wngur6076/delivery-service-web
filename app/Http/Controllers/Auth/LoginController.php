<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function store()
    {
        if (! $token = $this->guard()->attempt(request(['email', 'password']))) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '입력을 다시 확인해주세요.',
                'error' => '401'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => '로그인 성공했어요.',
        ], 200)->header('Authorization', $token);
    }

    public function show()
    {
        $user = User::find(auth()->user()->id);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function destroy()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'message' => '로그아웃 성공했어요',
        ], 200);
    }

    private function guard()
    {
        return auth()->guard();
    }
}
