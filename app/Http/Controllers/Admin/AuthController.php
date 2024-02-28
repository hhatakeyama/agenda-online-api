<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request){
        Log::info("Login attempt", [$request->email]);
        $credentials = $request->only(['email', 'password']);
        Log::info("credentials", [$credentials]);
        if (Auth::attempt($credentials)) {
            Log::info("user logged", [$request->email]);
            $request->user()->tokens()->where('name', $request->email)->delete();
            $token = $request->user()->createToken($request->email);
            return response()->json(['token' => $token->plainTextToken], 200);
        } else {
            Log::error("Error login user", [$request]);
            return response()->json([
                "message" => "Erro ao logar usuario. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
