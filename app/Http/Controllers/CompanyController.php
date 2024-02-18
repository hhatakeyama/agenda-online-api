<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Models\CompanyDaysOfWeek;
use App\Models\CompanyEmployee;
use App\Models\CompanyService;

class CompanyController extends Controller
{
    public function getAllDataFromCompany($id) {
        try {
            $company = Company::with("companyEmployees.employee", "companyServices.service.serviceCategory", "daysOfWeeks", "city")->findOrFail($id);
            $listServices = [];
            foreach($company->companyServices as $service) {
                $listEmployees = [];
                foreach($service->service->employeeServices as $employee) {
                    array_push($listEmployees, $employee->employee);
                }
                $service->service->employees = $listEmployees;
                array_push($listServices, [
                    "service_id" => $service->service_id,
                    "employees" => $listEmployees
                ]);                
            }

            Log::info("Searching companies id", [$company]);
            return response()->json([
                "data" => $company
            ], 200);
        } catch(\Exception $e) {
            Log::info("Companies not found", [$e]);
            return response()->json([
                "message" => "Empresa não possui unidades cadastradas."
            ], 200);
        }
    }
}