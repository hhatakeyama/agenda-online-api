<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\User;
use App\Models\EmployeeService;
use App\Models\Organization;

class EmployeeController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g', 'f'];
        Log::info("Searching all employees", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $services = $request->services ? explode(",", $request->services) : [];
            $employees = [];
            if (in_array($request->user()->type, ['g', 'f'])) {
                $employees = User::with("employeeServices")
                    ->where("type", "f")
                    ->where(function ($subquery) use ($search) {
                        $subquery->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
                    })
                    ->where("organization_id", $request->user()->organization_id);
            } else {
                $employees = User::with("organization", "employeeServices")
                    ->where("type", "f")
                    ->where(function ($subquery) use ($search) {
                        $subquery->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
                    });
                if ($request->organization_id) {
                    $employees = $employees->where("organization_id", $request->organization_id);
                }
            }
            if ($services) {
                $employees = $employees->whereHas("employeeServices", function ($query) use ($services) {
                    $query->whereIn("service_id", $services);
                });
            }
            return response()->json([
                "data" => $employees->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching employee id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $employee = null;
                if ($request->user()->type === 'g') {
                    $employee = User::with("employeeServices.service")
                        ->where("organization_id", $request->user()->organization_id)
                        ->where("type", "f")
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $employee = User::with("employeeServices.service")
                        ->where("type", "f")
                        ->where("id", $id)
                        ->firstOrFail();
                }
                return response()->json(["data" => $employee], 200);
            } catch (\Exception $e) {
                Log::info("Employee not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Funcionário não encontrado."], 403);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function create(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Creating employee", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:App\Models\User,email|max:255',
                'password' => 'required|max:255',
                'occupation' => 'max:255',
                'type' => 'required',
                'organization_id' => 'required|integer',
            ], [
                "email.unique" => "E-mail já utilizado",
            ]);
            $employee = User::create($request->all());
            $employee->password = Hash::make($request->password);
            if ($employee->save()) {
                Log::info("Employee created");
                if ($request->services) {
                    foreach ($request->services as $service_id) {
                        $this->createServicesEmployee($employee->id, $service_id);
                    }
                }
                $organization = Organization::find($request->organization_id);
                $data = [
                    'name' => $employee->name,
                    'organization' => $organization->name,
                ];
                try {
                    Mail::send('mails.novofuncionario', $data, function ($message) use ($employee) {
                        $message->to($employee->email);
                        $message->subject('Skedyou - Novo usuário');
                        $message->from('suporte@skedyou.com', 'Equipe Skedyou');
                    });
                } catch (\Exception $e) {
                    Log::error("Mail not sent", [$e->getMessage()]);
                }
                return response()->json(["message" => "Funcionário criado com sucesso"], 200);
            } else {
                Log::error("Error create employee", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao criar funcionário. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function createServicesEmployee($employee_id, $service_id)
    {
        Log::info("Service's employee", [$service_id]);
        $employeeService = EmployeeService::create([
            'employee_id' => $employee_id,
            'service_id' => $service_id,
        ]);
        if ($employeeService->save()) {
            Log::info("Service's employee created");
        } else {
            Log::error("Error create service's employee", [$employee_id, $service_id]);
        }
    }

    public function update(Request $request, User $employee)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating employee", [$request->employee, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes) || ($request->user()->type === 'f' && $request->user()->id === $employee->id)) {
            $emailFilled = $employee->email != $request->email;
            $validations = [
                'name' => 'required|max:255',
                'email' => 'required|max:255',
                'occupation' => 'max:255',
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
            $employee->update($request->all());
            if ($employee->save()) {
                Log::info("Employee updated");
                if ($request->services) {
                    $employee->employeeServices()->delete();
                    foreach ($request->services as $service_id) {
                        $this->createServicesEmployee($employee->id, $service_id);
                    }
                }
                return response()->json(["message" => "Funcionário atualizado com sucesso"], 200);
            } else {
                Log::info("Error updating employee", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao atualizar funcionário. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function updatePicture(Request $request, User $employee)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating photo", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes) || ($request->user()->type === 'f' && $request->user()->id === $employee->id)) {
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

    public function delete(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Inativation of the employee", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $employee = User::where("type", "f")->findOrFail($id);
                $employee->status = 0;
                $employee->save();
                Log::info("Employee inactivated successfully");
                return response()->json(["message" => "Funcionário inativado com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the employee", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar funcionário. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::info("Error updating employee");
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
