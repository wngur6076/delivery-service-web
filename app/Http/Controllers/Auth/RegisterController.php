<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function store()
    {
        $this->validate(request(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '입력하신 메일함에서 인증확인 메일을 확인해주세요.',
            'data' => $user,
        ], 201);
    }
}
