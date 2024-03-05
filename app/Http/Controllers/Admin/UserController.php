<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\Organization;
use App\Models\User;
use Exception;

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
                        ->whereNot("type", "f")
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $user = User::whereNot("type", "f")->findOrFail($id);
                }
                return response()->json(["data" => $user], 200);
            } catch (\Exception $e) {
                Log::info("Employee not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Usuário não encontrado."], 403);
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
                'email' => 'required|unique:App\Models\User,email|max:255',
                'password' => 'required',
                'type' => 'required',
                'organization_id' => 'required|integer',
            ], [
                "email.unique" => "E-mail já utilizado",
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
                $validations['email'] = ['required', 'string', 'email', 'max:255', 'unique:App\Models\User,email'];
            }
            if ($request->password) {
                $validations['password'] = ['required', 'string', 'confirmed'];
            }
            $request->validate($validations, [
                "email.unique" => "E-mail já utilizado",
            ]);
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

    public function updatePicture(Request $request, User $employee)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Updating photo", [$request->client, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $file = $request->file;
                $extensao = $file->extension();
                $extensao = ($extensao == "jpeg" ? "jpg" : $extensao);
                $filehash = uniqid(date('HisYmd'));
                $filename = $filehash . "." . $extensao;
    
                $filePath = "app/public/employees/";
                $this->gerarFotos($filePath, $filehash, $extensao, $file);
                $employee->picture = $filename;
            } catch (\Exception $e) {
                Log::error("Erro upload foto: " . $e->getMessage());
                return response()->json(collect(['message' => 'Erro ao salvar foto']), 401);
            }
            if ($employee->save()) {
                return response()->json(["message" => "Foto atualizada com sucesso"], 200);
            } else {
                Log::info("Error updating photo", [$request]);
                return response()->json([
                    "message" => "Erro ao atualizar foto. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::info("User without permission, tried to update picture");
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
    
    // Funções locais
    public function gerarFotos($filePath, $filehash, $extensao, $file, $rotate = 0)
    {
        $filename = $filehash . "." . $extensao;
        //Tamanho original
        $this->cropImage($filePath, "original-" . $filename, $extensao, $file, 0, 0, array(
            "rotate" => $rotate
        ), 100);
        //Tamanho 84x84px
        $this->cropImage($filePath, $filename, $extensao, $file, 84, 84, array(
            "rotate" => 0,
            "crop" => true
        ));
        //Tamanho 84x84px webp
        // $this->cropImage($filePath, $filehash . ".webp", "webp", $file, 84, 84, array(
        //     "rotate" => 0,
        //     "crop" => true
        // ));
    }

    public function cropImage($filePath, $filename, $extensao, $file, $imgWidth, $imgHeight, $options = array(), $qualidade = 80)
    {
        Log::info("Gerando foto: ", [$filePath, $filename]);
        $manager = new ImageManager(new Driver());
        $img = $manager->read($file);

        try {
            if ($extensao == "webp") {
                $img->encode($extensao);
            }

            if (isset($options["rotate"]) && $options["rotate"] != 0) {
                $img->rotate($options["rotate"]);
            }

            if (isset($options["crop"]) && $options["crop"]) {
                $dim = (intval($img->width()) / intval($img->height())) - ($imgWidth / $imgHeight);
                if ($dim > 0) {
                    $img->resize(null, $imgHeight, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->resizeCanvas(null, $imgHeight, 'center', true, 'ffffff');
                } else {
                    $img->resize($imgWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->resizeCanvas($imgWidth, null, 'center', true, 'ffffff');
                }
                $img->crop($imgWidth, $imgHeight);

                $filename = $imgWidth . "x" . $imgHeight . "-" . $filename;
            }

            $img->save(storage_path($filePath . $filename), $qualidade);
        } catch (\Exception $e) {
            Log::info("Erro ao gerar foto", [$e->getMessage()]);
        }
    }
}
