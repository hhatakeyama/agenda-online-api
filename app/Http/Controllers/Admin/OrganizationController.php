<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Searching all organizations", [$request->user()]);
        if ($request->user()->type && in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $organizations = Organization::where("registeredName", "LIKE", "%$search%")
                ->orWhere("tradingName", "LIKE", "%$search%")
                ->orWhere("cnpj", "LIKE", "%$search%")
                ->paginate($pageSize, ['*'], 'page', $page);
            return response()->json(["data" => $organizations], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a'];
        Log::info("Searching organization id", [$id, $request->user()]);
        if ($request->user()->type && in_array($request->user()->type, $allowedTypes)) {
            try {
                $organization = Organization::findOrFail($id);
                return response()->json(["data" => $organization], 200);
            } catch (\Exception $e) {
                Log::error("Error searching organization");
                return response()->json(["message" => "Empresa não encontrada."], 404);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function create(Request $request)
    {
        $allowedTypes = ['s', 'a'];
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
            $organization = Organization::create($request->all());
            if ($organization->save()) {
                Log::info("Organization created");
                return response()->json(["message" => "Empresa criada com sucesso"], 200);
            } else {
                Log::error("Error create organziation", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao criar empresa. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function update(Request $request, Organization $organization)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating organization", [$request->organization, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            // Validar validate
            // $request->validate([
            //     'registeredName' => 'required|unique:organizations,registeredName|max:255',
            //     'slug' => 'required|unique:organizations,slug|max:255',
            //     'tradingName' => 'required|unique:organizations,tradingName|max:255',
            //     'cnpj' => 'required|unique:organizations,cnpj|max:20',
            // ], [
            //     "registeredName.unique" => "Razão Social já existe; ",
            //     "slug.unique" => "Slug já existe; ",
            //     "tradingName.unique" => "Nome Fantasia já existe; ",
            //     "cnpj.unique" => "CNPJ já existe; ",
            // ]);
            $organization->update($request->all());
            if ($organization->save()) {
                return response()->json(["message" => "Empresa atualizada com sucesso"], 200);
            } else {
                Log::info("Error updating organization", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao atualizar Empresa. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
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
        $allowedTypes = ['s', 'a'];
        Log::info("Inativation of the organization", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $organization = Organization::findOrFail($id);
                $organization->status = 0;
                $organization->save();
                Log::info("Organization inactivated successfully");
                return response()->json(["message" => "Empresa inativada com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the organization", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar empresa. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}