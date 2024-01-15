<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Companies;
use App\Models\DaysOfWeek_Company;
use App\Models\Company_Employee;

class CompanyController extends Controller
{
    public function get()
    {
        Log::info("Searching all companies");
        $companies = Companies::paginate(10);;
        return response()->json([
            "data" => $companies
        ], 200);
    }

    public function getById($id)
    {
        try {
            $company = Companies::findOrFail($id);
            Log::info("Searching company id", [$company]);
            return response()->json([
                "data" => $company
            ], 200);
        } catch(\Exception $e) {
            Log::info("Company not found", [$id]);
            return response()->json([
                "message" => "Unidade não encontrada."
            ], 200);
        }
    }

    public function create(Request $request)
    {
        Log::info("Creating company");
        $validated = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required|max:255',
            'district' => 'required|max:255',
            'cep' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'thumb' => 'required',
            'organization_id' => 'required|integer',
            'phone' => 'required|max:255',
            'mobilePhone' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ]);
        $company = Companies::create($request->all());
        if($company->save()) {
            Log::info("Company created", [$company]);

            $company->createDaysofWeek($request, $company->id)->createMany($request->input('daysOfWeek'));
            $company->createEmployeesOfCompany($request, $company->id)->createMany($request->input('employees'));
            
            return response()->json([
                "message" => "Unidade criada com sucesso",
            ], 200);
        } else {
            Log::error("Error create company", [$request]);
            return response()->json([
                "message" => "Erro ao criar unidade. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    private function createDaysofWeek($request, $company_id)
    {
        $days_of_week = DaysOfWeek_Company::create($request->all());
        $days_of_week->company_id = $company_id;
        if($days_of_week->save()){
            Log::info("Days of week's company created", [$days_of_week]);
        } else {
            Log::error("Error create days of week's company", [$request]);
        }
    }

    private function createEmployeesOfCompany($request, $company_id)
    {
        $employees = Company_Employee::create($request->all());
        $employees->company_id = $company_id;
        if($employees->save()){
            Log::info("Employee's company created", [$employees]);
        } else {
            Log::error("Error create employee's company", [$request]);
        }
    }

    public function update(Request $request, Companies $company)
    {
        Log::info("Updating company", [$request]);
        $company->update($request->all());
        if($company->save()) {
            return response()->json([
                "message" => "Unidade atualizada com sucesso",
            ], 200);
        } else {
            Log::info("Error updating company", [$request]);
            return response()->json([
                "message" => "Erro ao atualizar unidade. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function delete($id)
    {
        try {
            $company = Companies::findOrFail($id); 
            Log::info("Inativation of the company $company");
            $company->status = false;
            $company->save();
            Log::info("Company inactivated successfully");
            return response()->json([
                "message" => "Unidade inativada com sucesso.",
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error inativation of the company $id");
            return response()->json([
                "message" => "Erro ao inativar unidade. Entre em contato com o administrador do site.",
            ], 400);
        }
    }
}