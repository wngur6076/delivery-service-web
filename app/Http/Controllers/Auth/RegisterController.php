<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *      path="/auth/register",
     *      tags={"Authorization"},
     *      summary="회원가입 하기",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"name", "email","password"},
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="Name"
     *                  ),
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
     *
     *
     *      @OA\Response(
     *          response=201,
     *          description="회원가입 성공",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *       ),
     *       @OA\Response(
     *          response=401,
     *          description="unauthorized"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="validate_error"
     *      ),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'address' => ['required'],
            'password' => ['required', 'min:8'],
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'address' => request('address'),
            'password' => request('password'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '회원가입 성공했어요.',
            'data' => $user,
        ], 201);
    }
}
