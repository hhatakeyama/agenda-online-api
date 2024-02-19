<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching all users", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $users = [];
            if ($request->user()->type === 'g') {
                $users = User::where(function ($subquery) use ($search) {
                    $subquery->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
                })->where("type", "g")->where("organization_id", $request->user()->organization_id);
            } else {
                $users = User::with("organization")
                    ->where(function ($subquery) use ($search) {
                        $subquery->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
                    })
                    ->whereNot("type", "f");
                if ($request->user()->type === "a") {
                    $users = $users->whereNot("type", "s");
                }
                if ($request->organization_id) {
                    $users = $users->where("organization_id", $request->organization_id);
                }
            }
            return response()->json([
                "data" => $users->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function me(Request $request)
    {
        Log::info("Retrieving me data", [$request->user()]);
        if ($request->user()) {
            return response()->json(['data' => $request->user()], 200);
        } else {
            return response()->json(['message' => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching user id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $user = null;
                if ($request->user()->type === 'g') {
                    $user = User::where("organization_id", $request->user()->organization_id)
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $user = User::findOrFail($id);
                }
                return response()->json(["data" => $user], 200);
            } catch (\Exception $e) {
                Log::info("Employee not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Usuario não encontrado."], 403);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function create(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Creating user", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|unique:users.email|max:255',
                'password' => 'required',
                'type' => 'required',
                'organization_id' => 'required|integer',
            ]);
            $user = User::create($request->all());
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                Log::info("User created");
                if ($user->type === "g") {
                    $organization = Organization::find($request->organization_id);
                    $data = [
                        'name' => $user->name,
                        'organization' => $organization->name,
                    ];
                    try {
                        Mail::send('mails.novousuario', $data, function ($message) use ($user) {
                            $message->to($user->email);
                            $message->subject('Skedyou - Novo usuário');
                            $message->from('suporte@skedyou.com', 'Equipe Skedyou');
                        });
                    } catch (Exception $e) {
                        Log::error("Mail not sent", [$e->getMessage()]);
                    }
                }
                return response()->json(["message" => "Usuario criado com sucesso"], 200);
            } else {
                Log::error("Error create user", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao criar usuario. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function update(Request $request, User $user)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating user", [$request->user, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $emailFilled = $user->email != $request->email;
            $validations = [
                'name' => 'required|max:255',
                'email' => 'required|max:255',
                'type' => 'required',
                'organization_id' => 'required|integer',
            ];
            if ($emailFilled) {
                $validations['email'] = ['required', 'string', 'email', 'max:255', 'unique:users.email'];
            }
            if ($request->password) {
                $validations['password'] = ['required', 'string', 'confirmed'];
            }
            $user->fill($request->all());
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            if ($user->save()) {
                return response()->json(["message" => "Usuário atualizado com sucesso"], 200);
            } else {
                Log::info("Error updating user", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao atualizar usuário. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function delete(Request $request, $id)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Inativation of the user", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $user = User::findOrFail($id);
                $user->status = 0;
                $user->save();
                Log::info("user inactivated successfully");
                return response()->json(["message" => "Usuário inativado com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the user", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar usuário. Entre em contato com o administrador do site.",
                ], 403);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}
