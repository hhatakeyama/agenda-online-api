<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceCategory;

class ServiceCategoryController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching all service categories", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $serviceCategories = [];
            if ($request->user()->type === 'g') {
                $serviceCategories = ServiceCategory::where("name", "LIKE", "%$search%")
                    ->where("organization_id", $request->user()->organization_id);
            } else {
                $serviceCategories = ServiceCategory::with("organization")
                    ->where("name", "LIKE", "%$search%");
                if ($request->organization_id) {
                    $serviceCategories = $serviceCategories->where("organization_id", $request->organization_id);
                }
            }
            return response()->json([
                "data" => $serviceCategories->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching category id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $serviceCategory = null;
                if ($request->user()->type === 'g') {
                    $serviceCategory = ServiceCategory::where("organization_id", $request->user()->organization_id)
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $serviceCategory = ServiceCategory::findOrFail($id);
                }
                return response()->json(["data" => $serviceCategory], 200);
            } catch (\Exception $e) {
                Log::info("Category not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Categoria não encontrada."], 403);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function create(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Creating category", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $request->validate([
                'name' => 'required|max:255',
                'organization_id' => 'required|integer',
            ]);
            $serviceCategory = ServiceCategory::create($request->all());
            if ($serviceCategory->save()) {
                Log::info("Category created");
                return response()->json(["message" => "Categoria criada com sucesso"], 200);
            } else {
                Log::error("Error create category", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao criar categoria. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating categoria", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            // Validar validate
            // $request->validate([
            //     'name' => 'required|max:255',
            //     'organization_id' => 'required|integer',
            // ]);
            $serviceCategory->update($request->all());
            if ($serviceCategory->save()) {
                return response()->json(["message" => "Categoria atualizada com sucesso"], 200);
            } else {
                Log::info("Error updating categoria", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao atualizar Categoria. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
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
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Inativation of the category", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $serviceCategory = ServiceCategory::findOrFail($id);
                $serviceCategory->status = 0;
                $serviceCategory->save();
                Log::info("Category inactivated successfully");
                return response()->json(["message" => "Categoria inativada com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the categoria", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar categoria. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}
