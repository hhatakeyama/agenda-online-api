<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Models\CompanyDaysOfWeek;
use App\Models\CompanyEmployee;
use App\Models\CompanyService;

class CompanyController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
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
                $companies = Company::with("organization", "companyEmployees", "companyServices")
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
                'thumb' => 'required',
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
        Log::info("Service's company", [$service]);
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
}
