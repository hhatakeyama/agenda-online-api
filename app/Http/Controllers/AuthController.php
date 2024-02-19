<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Client;

class AuthController extends Controller
{
    public function login(Request $request){
        Log::info("request", [$request]);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::guard('client')->attempt($credentials)) {
            $loggedUser = Auth::guard('client')->user();
            Log::info("client logged", [$loggedUser]);
            $client = Client::find($loggedUser->id);
            $client->tokens()->where('name', $request->email)->delete();
            $token = $client->createToken($request->email);
            return response()->json([
                'token' => $token->plainTextToken,
            ], 200);
        } else {
            Log::error("Error login client", [$request]);
            return response()->json([
                "message" => "E-mail ou senha inválidos",
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
