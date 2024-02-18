<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function get(Request $request)
    {
        if($request->user()->type === 's' || $request->user()->type === 'a') {
            Log::info("Searching all organizations");
            $search = $request->search;
            $organizations = Organization::where("registeredName", "LIKE", "%$search%")
                ->orWhere("tradingName", "LIKE", "%$search%")
                ->orWhere("cnpj", "LIKE", "%$search%")
                ->paginate(10);
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
        $allowedTypes = ['a', 's', 'g'];
        if (in_array($request->user()->type, $allowedTypes)) {
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

    public function create(Request $request)
    {
        $allowedTypes = ['a', 's'];
        if (in_array($request->user()->type, $allowedTypes)) {
            Log::info("Creating organization");
            $request->validate([
                'registeredName' => 'required|unique:organizations,registeredName|max:255',
                'slug' => 'required|unique:organizations,slug|max:255',
                'tradingName' => 'required|unique:organizations,tradingName|max:255',
                'cnpj' => 'required|unique:organizations,cnpj|max:20',
            ], [
                "registeredName.unique" => "Razão Social já existe; ",
                "slug.unique" => "Slug já existe; ",
                "tradingName.unique" => "Nome Fantasia já existe; ",
                "cnpj.unique" => "CNPJ já existe; ",
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
                "message" => "Você não tem permissão para criar a empresa.",
            ], 400);
        }
            
    }

    public function update(Request $request, Organization $organization)
    {
        $allowedTypes = ['a', 's', 'g'];
        if (in_array($request->user()->type, $allowedTypes)) {
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
                "message" => "Você não tem permissão para atualizar a empresa.",
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        $allowedTypes = ['a', 's'];
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $organization = Organization::findOrFail($request->id);        
                Log::info("Inativation of the organization", [$request->id]);
                $organization->status = 0;
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
                "message" => "Você não tem permissão para inativar a empresa.",
            ], 400);
        }
    }
}
