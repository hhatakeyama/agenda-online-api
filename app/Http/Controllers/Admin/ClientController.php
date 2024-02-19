<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\Client;

class ClientController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Searching all clients", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $clients = [];
            if ($request->user()->type === 'g') {
                $clients = Client::where("name", "LIKE", "%$search%")
                    ->where("organization_id", $request->user()->organization_id);
            } else {
                $clients = Client::where("name", "LIKE", "%$search%");
                if ($request->organization_id) {
                    $clients = $clients->where("organization_id", $request->organization_id);
                }
            }
            return response()->json([
                "data" => $clients->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Searching client id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $client = null;
                if ($request->user()->type === 'g') {
                    $client = Client::where("organization_id", $request->user()->organization_id)
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $client = Client::findOrFail($id);
                }
                return response()->json(["data" => $client], 200);
            } catch (\Exception $e) {
                Log::info("Client not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Cliente não encontrado."], 403);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function create(Request $request)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Creating client", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|confirmed|max:255',
            ]);
            $client = Client::create($request->all());
            $client->password = Hash::make($request->password);
            $token = $client->createToken($request->email, ['server:update']);
            if ($client->save()) {
                Log::info("Client created");
                $data = array('name' => $client->name);
                Mail::send('mails.cadastro', $data, function ($message) use ($client) {
                    $message->to($client->email);
                    $message->subject('Skedyou - Cadastro efetuado com sucesso!');
                    $message->from('suporte@skedyou.com', 'Equipe Skedyou');
                });

                return response()->json([
                    "token" => $token->plainTextToken,
                    "message" => "Cliente criado com sucesso",
                ], 200);
            } else {
                Log::error("Error create client", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao criar cliente. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function update(Request $request, Client $client)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Updating client", [$request->client, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $emailFilled = $client->email != $request->email;
            $validations = [
                'name' => 'required|max:255',
                'email' => 'required|max:255',
            ];
            $client->name = $request->name;
            if ($emailFilled) {
                $validations['email'] = ['required', 'string', 'email', 'max:255', 'unique:clients'];
                $client->email = $request->email;
            }
            if ($request->password) {
                $validations['password'] = ['required', 'string', 'confirmed'];
                $client->password = Hash::make($request->password);
            }
            $request->validate($validations);
            if ($client->save()) {
                return response()->json(["message" => "Cliente atualizado com sucesso"], 200);
            } else {
                Log::info("Error updating client", [$request]);
                return response()->json([
                    "message" => "Erro ao atualizar cliente. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::info("User without permission, tried to update client");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function updatePicture(Request $request, Client $client)
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
    
                $filePath = "app/public/clients/";
                $this->gerarFotos($filePath, $filehash, $extensao, $file);
                $client->picture = $filename;
            } catch (\Exception $e) {
                Log::error("Erro upload foto: " . $e->getMessage());
                return response()->json(collect(['message' => 'Erro ao salvar foto']), 401);
            }
            if ($client->save()) {
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

    public function delete(Request $request, $id)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Inativation of the client", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $client = Client::findOrFail($id);
                $client->status = 0;
                $client->save();
                Log::info("Client inactivated successfully");
                return response()->json(["message" => "Cliente inativado com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the client", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar cliente. Entre em contato com o administrador do site.",
                ], 400);
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
