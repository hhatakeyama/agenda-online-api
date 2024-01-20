<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Organization;
use App\Models\Company;

class OrganizationController extends Controller
{
    public function get(Request $request)
    {
        if($request->user()->type === 's' || $request->user()->type === 'a') {
            Log::info("Searching all organizations");
            $organizations = Organization::paginate(10);
            return response()->json([
                "data" => $organizations
            ], 200);
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Você não tem permissão para criar uma empresa.",
            ], 400);
        }
    }

    public function getById(Request $request)
    {
        if($request->user()->type === 's' || $request->user()->type === 'a') {
            try {
                $organization = Organization::findOrFail($request->id);
                Log::info("Searching organization id" [$request->id]);
                return response()->json([
                    "data" => $organization
                ], 200);
            } catch(\Exception $e) {
                Log::error("Error searching organization", [$request]);
                return response()->json([
                    "message" => "Empresa não encontrada.",
                ], 400);
            }
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Você não tem permissão para criar uma empresa.",
            ], 400);
        }
    }

    public function getCompaniesFromOrganization(Request $request)
    {
        try {
            $organization = Organization::findOrFail($request->id);
            $organization->companies = Company::where('organization_id', $request->id)->where('status', 1)->select('id', 'name')->get();
            Log::info("Searching companies from organization id", [$request->id]);
            return response()->json([
                "data" => $organization
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error searching companies from organization", [$request->id]);
            return response()->json([
                "message" => "Unidades não encontradas.",
            ], 400);
        }
    }

    public function create(Request $request)
    {
        Log::info($request->user()->type);
        if($request->user()->type === 's' || $request->user()->type === 'a') {
            Log::info("Creating organization");
            $validated = $request->validate([
                'registeredName' => 'required|max:255',
                'tradingName' => 'required|max:255',
                'cnpj' => 'required|max:20',
            ]);
            $organizations = Organization::create($request->all());
            if($organizations->save()) {
                Log::info("Organization created", [$organizations]);
                return response()->json([
                    "message" => "Empresa criada com sucesso",
                ], 200);
            } else {
                Log::error("Error create organziation", [$request]);
                return response()->json([
                    "message" => "Erro ao criar empresa. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Você não tem permissão para criar uma empresa.",
            ], 400);
        }
            
    }

    public function update(Request $request, Organization $organization)
    {
        if($request->user()->type === 's' || $request->user()->type === 'a') {
            Log::info("Updating organization", [$request->id]);
            $organization->update($request->all());
            if($organization->save()) {
                return response()->json([
                    "message" => "Empresa atualizada com sucesso",
                ], 200);
            } else {
                Log::info("Error updating organization", [$request]);
                return response()->json([
                    "message" => "Erro ao atualizar Empresa. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Você não tem permissão para criar uma empresa.",
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        if($request->user()->type === 's' || $request->user()->type === 'a') {
            try {
                $organization = Organization::findOrFail($request->id);        
                Log::info("Inativation of the organization", [$request->id]);
                $organization->status = false;
                $organization->save();
                Log::info("Organization inactivated successfully");
                return response()->json([
                    "message" => "Empresa inativada com sucesso.",
                ], 200);
            } catch(\Exception $e) {
                Log::error("Error inativation of the organization $request->id");
                return response()->json([
                    "message" => "Erro ao inativar empresa. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Você não tem permissão para criar uma empresa.",
            ], 400);
        }
    }
}
