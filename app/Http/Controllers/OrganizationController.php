<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Organizations;
use App\Models\Companies;

class OrganizationController extends Controller
{
    public function get()
    {
        Log::info("Searching all organizations");
        $organizations = Organizations::paginate(10);
        return response()->json([
            "data" => $organizations
        ], 200);
    }

    public function getById($id)
    {
        try {
            $organization = Organizations::findOrFail($id);
            Log::info("Searching organization id $id");
            return response()->json([
                "data" => $organization
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error searching organization", [$id]);
            return response()->json([
                "message" => "Empresa não encontrada.",
            ], 400);
        }
    }

    public function getCompaniesFromOrganization($id){
        try {
            $organization = Organizations::findOrFail($id);
            $organization->companies = Companies::where('organization_id', $id)->where('status', 1)->select('id', 'name')->get();
            Log::info("Searching companies from organization id $id");
            return response()->json([
                "data" => $organization
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error searching companies from organization", [$id]);
            return response()->json([
                "message" => "Unidades não encontradas.",
            ], 400);
        }
    }

    public function create(Request $request)
    {
        Log::info("Creating organization");
        $validated = $request->validate([
            'registeredName' => 'required|max:255',
            'tradingName' => 'required|max:255',
            'cnpj' => 'required|max:20',
        ]);
        $organizations = Organizations::create($request->all());
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
    }

    public function update(Request $request, Organizations $organization)
    {
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
    }

    public function delete($id)
    {
        try {
            $organization = Organizations::findOrFail($id);        
            Log::info("Inativation of the organization $id");
            $organization->status = false;
            $organization->save();
            Log::info("Organization inactivated successfully");
            return response()->json([
                "message" => "Empresa inativada com sucesso.",
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error inativation of the organization $id");
            return response()->json([
                "message" => "Erro ao inativar empresa. Entre em contato com o administrador do site.",
            ], 400);
        }
    }
}
