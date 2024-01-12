<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Companies;

class CompanyController extends Controller
{
    public function get()
    {
        Log::info("Searching all companies");
        $companies = Companies::all();
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
        $uuid = Str::uuid('id')->toString();
        $companies = Companies::create($request->all());
        if($companies->save()) {
            Log::info("Company created", [$companies]);
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