<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceCategory;

class ServiceCategoryController extends Controller
{
    public function get()
    {
        Log::info("Searching all categories");
        $serviceCategorys = ServiceCategory::paginate(10);
        return response()->json([
            "data" => $serviceCategorys
        ], 200);
    }

    public function getById(Request $request)
    {
        if($request->user()->type !== 'f') {
            try {
                $serviceCategory = ServiceCategory::findOrFail($request->id);
                Log::info("Searching category id $serviceCategory");
                return response()->json([
                    "data" => $serviceCategory
                ], 200);
            } catch(\Exception $e) {
                Log::info("Category not found", [$request->id]);
                return response()->json([
                    "message" => "Categoria não encontrada."
                ], 200);
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
        if($request->user()->type !== 'f') {
            Log::info("Creating category", [$request]);
            $validated = $request->validate([
                'name' => 'required|max:255',
                'organization_id' => 'required|integer',
            ]);
            $serviceCategorys = ServiceCategory::create($request->all());
            if($serviceCategorys->save()) {
                Log::info("Category created", [$serviceCategorys]);
                return response()->json([
                    "message" => "Categoria criada com sucesso",
                ], 200);
            } else {
                Log::error("Error create category", [$request]);
                return response()->json([
                    "message" => "Erro ao criar categoria. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Você não tem permissão para criar uma empresa.",
            ], 400);
        }
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        if($request->user()->type !== 'f') {
            Log::info("Updating categoria", [$request]);
            $serviceCategory->update($request->all());
            if($serviceCategory->save()) {
                return response()->json([
                    "message" => "Categoria atualizada com sucesso",
                ], 200);
            } else {
                Log::info("Error updating categoria", [$request]);
                return response()->json([
                    "message" => "Erro ao atualizar Categoria. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Você não tem permissão para criar uma categoria.",
            ], 400);
        }
    }

    public function delete(Request $request, $id)
    {
        if($request->user()->type !== 'f') {
            try {
                $serviceCategory = ServiceCategory::findOrFail($id); 
                Log::info("Inativation of the category $serviceCategory");
                $serviceCategory->status = false;
                $serviceCategory->save();
                Log::info("Category inactivated successfully");
                return response()->json([
                    "message" => "Categoria inativada com sucesso.",
                ], 200);
            } catch(\Exception $e) {
                Log::error("Error inativation of the categoria", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar categoria. Entre em contato com o administrador do site.",
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
