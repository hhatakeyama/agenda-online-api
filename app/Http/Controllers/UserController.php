<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function get()
    {
        Log::info("Searching all users");
        $users = User::paginate(10);
        return response()->json([
            "data" => $users
        ], 200);
    }

    public function me(Request $request)
    {
        Log::info("Searching me ");
        Log::info("Searching me ", [$request]);
        return response()->json([
            'data' => $request->user()
        ], 200);
    }

    public function getById($id)
    {
        try {
            $user = User::findOrFail($id);
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
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'password' => 'required',
            'type' => 'required',
            'organization_id' => 'required|integer',
        ]);
        $users = User::create($request->all());
        $users->password = Hash::make($request->password);
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

    public function update(Request $request, User $user)
    {
        Log::info("Updating user", [$request->id]);
        $emailFilled = $user->email != $request->email;
        $validations = [
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'type' => 'required',
            'organization_id' => 'required|integer',
        ];
        if($emailFilled) {
            $regras['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];
        }
        if ($request->password) {
            $validations['password'] = ['required', 'string', 'confirmed'];
        }
        $user->fill($request->all());
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        if($user->save()) {
            return response()->json([
                "message" => "Usuario atualizado com sucesso",
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
            $user = User::findOrFail($id); 
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
