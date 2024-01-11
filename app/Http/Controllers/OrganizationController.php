<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Organizations;

class OrganizationController extends Controller
{
    public function get()
    {
        Log::info("Searching all organizations");
        $organizations = Organizations::all();
        return response()->json([
            "data" => $organizations
        ], 200);
    }

    public function getById(Organization $organization)
        {
        Log::info("Searching organization id $organization");
        return response()->json([
            "data" => $organization
        ], 200);
    }

    public function create(Request $request)
    {
        Log::info("Creating organization");
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
        Log::info("Updating organization", [$request]);
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

    public function delete(Organizations $organization)
     {
        Log::info("Inativation of the organization $organization");
        $organization->status = false;
        if($organization->save()) {
            Log::info("Organization inactivated successfully");
            return response()->json([
                "message" => "Empresa inativada com sucesso.",
            ], 200);
        } else {
            Log::error("Error inativation of the organization $organization");
            return response()->json([
                "message" => "Erro ao inativar empresa. Entre em contato com o administrador do site.",
            ], 400);
        }
    }
}
