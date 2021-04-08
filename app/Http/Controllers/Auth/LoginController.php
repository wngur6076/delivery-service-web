<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"Authorization"},
     *      summary="로그인 하기",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"email","password"},
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="Email"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      description="Password"
     *                  ),
     *             )
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="로그인 성공",
     *       ),
     *       @OA\Response(
     *          response=401,
     *          description="unauthorized"
     *      ),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     */
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
            'data' => [
                'ttl' => config('jwt.ttl'),
                'refresh_ttl' => config('jwt.refresh_ttl'),
            ],
        ], 200)->header('Authorization', $token);
    }

    /**
     * @OA\Get(
     *     path="/auth/user",
     *     tags={"Authorization"},
     *     summary="유저정보 가져오기",
     *
     *     @OA\Response(
     *         response=200,
     *         description="응답 성공"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show()
    {
        $user = User::find(auth()->user()->id);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/auth/logout",
     *     tags={"Authorization"},
     *     summary="로그아웃 하기",
     *
     *     @OA\Response(
     *         response=200,
     *         description="로그아웃 성공"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
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
