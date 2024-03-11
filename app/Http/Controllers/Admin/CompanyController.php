<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\Company;
use App\Models\CompanyDaysOfWeek;
use App\Models\CompanyEmployee;
use App\Models\CompanyService;
use App\Models\Service;

class CompanyController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g', 'f'];
        Log::info("Searching all companies", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $companies = [];
            if ($request->user()->type === 'g') {
                $companies = Company::with("organization", "companyEmployees", "companyServices", "daysOfWeeks")
                    ->where("name", "LIKE", "%$search%")
                    ->where("organization_id", $request->user()->organization_id);
            } else {
                $companies = Company::with("organization", "companyEmployees", "companyServices", "daysOfWeeks")
                    ->where("name", "LIKE", "%$search%");
                if ($request->organization_id) {
                    $companies = $companies->where("organization_id", $request->organization_id);
                }
            }
            return response()->json([
                "data" => $companies->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching company id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $company = null;
                if ($request->user()->type === 'g') {
                    $company = Company::with("companyEmployees.employee", "companyServices.service.serviceCategory", "daysOfWeeks", "city")
                        ->where("organization_id", $request->user()->organization_id)
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $company = Company::with("companyEmployees.employee", "companyServices.service.serviceCategory", "daysOfWeeks", "city")->findOrFail($id);
                }
                return response()->json(["data" => $company], 200);
            } catch (\Exception $e) {
                Log::info("Company not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Unidade não encontrada."], 403);
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
        Log::info("Creating company", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $request->validate([
                'name' => 'required|max:255',
                'address' => 'required|max:255',
                'district' => 'required|max:255',
                'cep' => 'required|max:255',
                'city_id' => 'required|max:255',
                'state' => 'required|max:255',
                'organization_id' => 'required|integer',
                'phone' => 'required|max:255',
                'mobilePhone' => 'required|max:255',
            ]);
            $company = Company::create($request->all());
            if ($company->save()) {
                Log::info("Company created");

                if ($request->daysOfWeek) {
                    foreach ($request->daysOfWeek as $dayOfWeek) {
                        $this->createDaysofWeek($company->id, $dayOfWeek);
                    }
                }

                if ($request->services) {
                    foreach ($request->services as $service) {
                        $this->createServiceCompany($company->id, $service);
                    }
                }

                if ($request->employees) {
                    foreach ($request->employees as $employee) {
                        $this->createEmployeesOfCompany($company->id, $employee);
                    }
                }

                return response()->json(["message" => "Unidade criada com sucesso"], 200);
            } else {
                Log::error("Error create company", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao criar unidade. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    private function createDaysofWeek($company_id, $dayOfWeek)
    {
        Log::info("Days of week's company", [$dayOfWeek]);
        $day_of_week = CompanyDaysOfWeek::create([
            'day_of_week' => $dayOfWeek['day_of_week'],
            'company_id' => $company_id,
            'start_time' => $dayOfWeek['start_time'],
            'end_time' => $dayOfWeek['end_time'],
            'start_time_2' => $dayOfWeek['start_time_2'],
            'end_time_2' => $dayOfWeek['end_time_2'],
            'start_time_3' => $dayOfWeek['start_time_3'],
            'end_time_3' => $dayOfWeek['end_time_3'],
            'start_time_4' => $dayOfWeek['start_time_4'],
            'end_time_4' => $dayOfWeek['end_time_4'],
        ]);

        if ($day_of_week->save()) {
            Log::info("Days of week's company created", [$day_of_week]);
        } else {
            Log::error("Error create days of week's company", [$dayOfWeek]);
        }
    }

    private function createServiceCompany($company_id, $service)
    {
        Log::info("Create Company Service", [$service]);
        $newService = CompanyService::create([
            'company_id' => $company_id,
            'service_id' => $service['service_id'],
            'price' => $service['price'],
            'duration' => $service['duration'],
            'description' => $service['description'],
            'send_email' => $service['send_email'],
            'send_sms' => $service["send_sms"],
            'email_message' => $service['email_message'],
            'sms_message' => $service['sms_message'],
        ]);
        if ($newService->save()) {
            Log::info("Service's company created", [$newService]);
        } else {
            Log::error("Error create company's employee", [$service]);
        }
    }

    private function createEmployeesOfCompany($company_id, $employee_id)
    {
        Log::info("Employee's company", [$employee_id]);
        $employee = CompanyEmployee::create([
            'company_id' => $company_id,
            'employee_id' => $employee_id,
        ]);
        $employee->company_id = $company_id;
        if ($employee->save()) {
            Log::info("Employee's company created", [$employee]);
        } else {
            Log::error("Error create employee's company", [$employee_id]);
        }
    }

    // Validar para type g
    public function update(Request $request, Company $company)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating company", [$request->company, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            // Validar validate
            // $request->validate([
            //     'name' => 'required|max:255',
            //     'address' => 'required|max:255',
            //     'district' => 'required|max:255',
            //     'cep' => 'required|max:255',
            //     'city_id' => 'required|max:255',
            //     'state' => 'required|max:255',
            //     'thumb' => 'required',
            //     'organization_id' => 'required|integer',
            //     'phone' => 'required|max:255',
            //     'mobilePhone' => 'required|max:255',
            // ]);
            $company->update($request->all());
            if ($company->save()) {
                return response()->json(["message" => "Unidade atualizada com sucesso"], 200);
            } else {
                Log::info("Error updating company", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao atualizar unidade. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function updateThumb(Request $request, Company $company)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating photo", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $file = $request->file;
                $extensao = $file->extension();
                $extensao = ($extensao == "jpeg" ? "jpg" : $extensao);
                $filehash = uniqid(date('HisYmd'));
                $filename = $filehash . "." . $extensao;

                $filePath = "app/public/companies/";
                $this->gerarFotos($filePath, $filehash, $extensao, $file);
                $company->thumb = $filename;
            } catch (\Exception $e) {
                Log::error("Erro upload foto: " . $e->getMessage());
                return response()->json(collect(['message' => 'Erro ao salvar foto']), 401);
            }
            if ($company->save()) {
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
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Inativation of the company", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $company = Company::findOrFail($id);
                $company->status = 0;
                $company->save();
                Log::info("Company inactivated successfully");
                return response()->json(["message" => "Unidade inativada com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the company", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar unidade. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
    
    public function createService(Request $request, Company $company)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Add company service", [$company->id, $request->service_id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $service = Service::findOrFail($request->service_id);
                $newService = CompanyService::create([
                    'company_id' => $company->id,
                    'service_id' => intval($request->service_id),
                    'price' => $service['price'],
                    'duration' => $service['duration'],
                    'description' => $service['description'],
                    'send_email' => $service['send_email'],
                    'send_sms' => $service["send_sms"],
                    'email_message' => $service['email_message'],
                    'sms_message' => $service['sms_message'],
                ]);
                if ($newService->save()) {
                    Log::info("Company Service added successfully", [$newService]);
                    return response()->json(["message" => "Serviço adicionado com sucesso."], 200);
                } else {
                    Log::error("Error adding company service", [$service]);
                    return response()->json([
                        "message" => "Erro ao adicionar serviço da unidade. Entre em contato com o administrador do site.",
                    ], 400);
                }
            } catch (\Exception $e) {
                Log::error("Error adding company service", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao adicionar serviço da unidade. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
    
    public function updateService(Request $request, Company $company, CompanyService $companyService)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Update company service", [$company->id, $companyService->id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $service = Service::findOrFail($companyService->service_id);
                $companyService->service_id = intval($request->service_id);
                $companyService->price = $request->has("price") ? $request->price : $service['price'];
                $companyService->duration = $request->has("duration") ? $request->duration : $service['duration'];
                $companyService->description = $request->has("description") ? $request->description : $service['description'];
                $companyService->send_email = $request->has("send_email") ? $request->send_email : $service['send_email'];
                $companyService->send_sms = $request->has("send_sms") ? $request->send_sms : $service["send_sms"];
                $companyService->email_message = $request->has("email_message") ? $request->email_message : $service['email_message'];
                $companyService->sms_message = $request->has("sms_message") ? $request->sms_message : $service['sms_message'];
                if ($companyService->save()) {
                    Log::info("Company Service updated successfully", [$companyService]);
                    return response()->json(["message" => "Serviço atualizado com sucesso."], 200);
                } else {
                    Log::error("Error updating company service", [$companyService]);
                    return response()->json([
                        "message" => "Erro ao atualizar serviço da unidade. Entre em contato com o administrador do site.",
                    ], 400);
                }
            } catch (\Exception $e) {
                Log::error("Error updating company service", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao atualizar serviço da unidade. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
    
    public function deleteService(Request $request, Company $company, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Delete company service", [$company->id, $id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $companyService = CompanyService::where("company_id", $company->id)->where("service_id", $id)->firstOrFail();
                $companyService->delete();
                Log::info("Company Service removed successfully");
                return response()->json(["message" => "Serviço removido com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error removing company service", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao remover serviço da unidade. Entre em contato com o administrador do site.",
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
        $this->cropImage($filePath, $filename, $extensao, $file, 733, 100, array(
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
