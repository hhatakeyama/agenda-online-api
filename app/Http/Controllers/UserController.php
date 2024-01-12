<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Users;

class UserController extends Controller
{
    public function get()
    {
        Log::info("Searching all users");
        $users = Users::all();
        return response()->json([
            "data" => $users
        ], 200);
    }

    public function getById($id)
    {
        try {
            $user = Users::findOrFail($id);
            Log::info("Searching user id ", [$user]);
            return response()->json([
                "data" => $user
            ], 200);
        } catch(\Exception $e) {
            Log::info("Employee not found", [$id]);
            return response()->json([
                "message" => "Usuario não encontrado."
            ], 200);
        }
    }

    public function create(Request $request)
    {
        Log::info("Creating user");
        $users = Users::create($request->all());
        if($users->save()) {
            Log::info("user created", [$users]);
            return response()->json([
                "message" => "Usuario criado com sucesso",
            ], 200);
        } else {
            Log::error("Error create user", [$request]);
            return response()->json([
                "message" => "Erro ao criar usuario. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function update(Request $request, Users $user)
    {
        Log::info("Updating user", [$request->id]);
        $user->update($request->all());
        if($user->save()) {
            return response()->json([
                "message" => "Usuario atualizada com sucesso",
            ], 200);
        } else {
            Log::info("Error updating user", [$request]);
            return response()->json([
                "message" => "Erro ao atualizar usuario. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function delete($id)
    {
        try {
            $user = Users::findOrFail($id); 
            Log::info("Inativation of the user $user");
            $user->status = false;
            $user->save();
            Log::info("user inactivated successfully");
            return response()->json([
                "message" => "Funcionario inativado com sucesso.",
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error inativation of the user $id");
            return response()->json([
                "message" => "Erro ao inativar usuario. Entre em contato com o administrador do site.",
            ], 400);
        }
    }
}
