<?php

namespace App\Http\Controllers\Admin;

date_default_timezone_set("America/Sao_Paulo");

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\Client;
use App\Models\OrganizationClient;

class ClientController extends Controller {
    public function get(Request $request) {
        $allowedTypes = ['s', 'a', 'g', 'f'];
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $status = $request->status;
            $selected = $request->selected;
            $clients = Client::where(function ($query) use ($search, $selected) {
                if (!empty($search) && !empty($selected))
                    $query->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%")->orWhere("id", $selected);
                else if (!empty($selected))
                    $query->where("id", $selected);
                else if (!empty($search))
                    $query->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
            });
            // if (in_array($request->user()->type, ['g', 'f'])) {
            //     $clients = $clients->whereHas("organizationClients", function ($query) use ($request) {
            //         $query->where("organization_id", $request->user()->organization_id);
            //     });
            // }
            if (!empty($status)) {
                $clients = $clients->where("status", $status);
            }
            return response()->json([
                "data" => $clients->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getProtected(Request $request) {
        $allowedTypes = ['s', 'a', 'g', 'f'];
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $status = $request->status;
            $selected = $request->selected;
            $clients = Client::where(function ($query) use ($search, $selected) {
                if (!empty($search) && !empty($selected))
                    $query->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%")->orWhere("id", $selected);
                else if (!empty($selected))
                    $query->where("id", $selected);
                else if (!empty($search))
                    $query->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
            });
            if (in_array($request->user()->type, ['g', 'f'])) {
                $clients = $clients->whereHas("organizationClients", function ($query) use ($request) {
                    $query->where("organization_id", $request->user()->organization_id);
                });
            }
            if (!empty($status)) {
                $clients = $clients->where("status", $status);
            }
            return response()->json([
                "data" => $clients->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id) {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching client id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $client = null;
                if ($request->user()->type === 'g') {
                    $client = Client::whereHas("organizationClients", function ($query) use ($request) {
                        $query->where("organization_id", $request->user()->organization_id);
                    })
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

    public function create(Request $request) {
        $allowedTypes = ['s', 'a', 'g', 'f'];
        Log::info("Creating client", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $validations = [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:App\Models\Client,email|max:255',
            ];
            if (!$request->autoPassword) {
                array_push(['password' => 'required|confirmed|max:255'], $validations);
            }
            $request->validate($validations, [
                "email.unique" => "E-mail já cadastrado",
            ]);
            $clientData = $request->all();
            $generatedPassword = Str::random(15);
            $clientData["password"] = Hash::make($generatedPassword);

            $client = Client::create($clientData);
            if (!$request->autoPassword) {
                $generatedPassword = $request->password;
            }
            $client->password = Hash::make($generatedPassword);
            Log::info($client);
            $token = $client->createToken($request->email, ['server:update']);
            if ($client->save()) {
                Log::info("Client created");

                if ($request->user()->organization_id) {
                    $this->createOrganizationClient($request->user(), $client->id);
                }

                if ($request->sendRegisterNotification === "1") {
                    \App\Jobs\SendClientRegistrationJob::dispatch($client, $generatedPassword);
                }

                return response()->json([
                    "client" => $client,
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

    public function update(Request $request, Client $client) {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating client", [$request->client, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $emailFilled = $client->email != $request->email;
            $validations = [
                'name' => 'required|max:255',
                'email' => 'required|max:255',
            ];
            $client->name = $request->name;
            $client->mobilePhone = $request->mobilePhone;
            $client->birthday = $request->birthday;
            if ($emailFilled) {
                $validations['email'] = ['required', 'string', 'email', 'max:255', 'unique:App\Models\Client,email'];
                $client->email = $request->email;
            }
            if ($request->password) {
                $validations['password'] = ['required', 'string', 'confirmed'];
                $client->password = Hash::make($request->password);
            }
            $request->validate($validations, [
                "email.unique" => "E-mail já cadastrado",
            ]);
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

    public function updatePicture(Request $request, Client $client) {
        $allowedTypes = ['s', 'a'];
        Log::info("Updating photo", [$request->user()]);
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

    public function delete(Request $request, $id) {
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
    private function createOrganizationClient($user, $clientId) {
        Log::info("Checking create organization client", [$user, $clientId]);
        $organizationClientData = ["client_id" => $clientId, "organization_id" => $user->organization_id];
        $organizationClient = OrganizationClient::where($organizationClientData)->first();
        if (!$organizationClient) {
            OrganizationClient::create($organizationClientData);
        }
    }

    private function gerarFotos($filePath, $filehash, $extensao, $file, $rotate = 0) {
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

    private function cropImage($filePath, $filename, $extensao, $file, $imgWidth, $imgHeight, $options = array(), $qualidade = 80) {
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
