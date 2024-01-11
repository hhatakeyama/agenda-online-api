<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceCategories;

class ServiceCategoryController extends Controller
{
    public function get()
        {
            Log::info("Searching all categories");
            $categorias = ServiceCategories::all();
            return response()->json([
                "data" => $categorias
            ], 200);
        }

    public function getById(ServiceCategories $serviceCategory)
        {
        Log::info("Searching category id $serviceCategory");
        return response()->json([
            "data" => $serviceCategory
        ], 200);
    }

    public function create(Request $request)
    {
        Log::info("Creating category");
        $categorias = ServiceCategories::create($request->all());
        if($categorias->save()) {
            Log::info("Category created", [$categorias]);
            return response()->json([
                "message" => "Categoria criada com sucesso",
            ], 200);
        } else {
            Log::error("Error create category", [$request]);
            return response()->json([
                "message" => "Erro ao criar categoria. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function update(Request $request, ServiceCategories $categoria)
    {
        Log::info("Updating categoria", [$request]);
        $categoria->update($request->all());
        if($categoria->save()) {
            return response()->json([
                "message" => "Categoria atualizada com sucesso",
            ], 200);
        } else {
            Log::info("Error updating categoria", [$request]);
            return response()->json([
                "message" => "Erro ao atualizar Categoria. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function delete(ServiceCategories $categoria)
     {
        Log::info("Inativation of the category $categoria");
        $categoria->status = false;
        if($categoria->save()) {
            Log::info("Category inactivated successfully");
            return response()->json([
                "message" => "Categoria inativada com sucesso.",
            ], 200);
        } else {
            Log::error("Error inativation of the categoria $categoria");
            return response()->json([
                "message" => "Erro ao inativar categoria. Entre em contato com o administrador do site.",
            ], 400);
        }
    }
}
