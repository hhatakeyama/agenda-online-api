<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Service;

class ServiceController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching all services", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $services = [];
            if ($request->user()->type === 'g') {
                $services = Service::where("name", "LIKE", "%$search%")
                    ->where("organization_id", $request->user()->organization_id);
            } else {
                $services = Service::with("organization")
                    ->where("name", "LIKE", "%$search%");
                if ($request->organization_id) {
                    $services = $services->where("organization_id", $request->organization_id);
                }
            }
            return response()->json([
                "data" => $services->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching service id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $service = null;
                if ($request->user()->type === 'g') {
                    $service = Service::where("organization_id", $request->user()->organization_id)
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $service = Service::findOrFail($id);
                }
                return response()->json(["data" => $service], 200);
            } catch (\Exception $e) {
                Log::info("Service not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Serviço não encontrada."], 403);
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
        Log::info("Creating service", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $request->validate([
                'name' => 'required|max:255',
                'description' => 'required|max:255',
                'price' => 'required',
                'duration' => 'required',
                'serviceCategory_id' => 'required|integer',
                'organization_id' => 'required|integer',
            ]);
            $service = Service::create($request->all());
            if ($service->save()) {
                Log::info("Service created");
                return response()->json(["message" => "Serviço criado com sucesso"], 200);
            } else {
                Log::error("Error create service", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao criar servico. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function update(Request $request, Service $service)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating service", [$request->service, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $service->update($request->all());
            if ($service->save()) {
                return response()->json(["message" => "Serviço atualizado com sucesso"], 200);
            } else {
                Log::info("Error updating service", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao atualizar serviço. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::info("User without permission tried to update service");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    // Validar para type g
    public function delete(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Inativation of the service", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $service = Service::findOrFail($id);
                $service->status = 0;
                $service->save();
                Log::info("Service inactivated successfully");
                return response()->json(["message" => "Serviço inativado com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the service", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar servico. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}
