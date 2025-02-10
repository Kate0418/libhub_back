<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthSignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request) 
    {
        $validated = $request->validated();

        if (Auth::attempt(['email' => $validated["email"], 'password' => $validated["password"]])) {
            // 認証成功時の処理
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'authToken' => $token,
            ]);
        } else {
            // 認証失敗時の処理
            return response()->json([
                'success' => false,
                'messages' => [
                    'メールアドレスまたはパスワードが正しくありません。',
                ]
            ], 401);
        }
    }

    public function signUp(AuthSignUpRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'email' => $validated["email"],
            'password' => $validated["password"],
        ]);

        Auth::login($user);
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'success' => true,
            'authToken' => $token,
        ]);
    }
}
