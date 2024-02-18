<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Service;

class ServiceController extends Controller
{
    public function get()
    {
        Log::info("Searching all services");
        $services = Service::paginate(10);
        return response()->json([
            "data" => $services
        ], 200);
    }

    public function getById($id)
    {
        try {
            $service = Service::findOrFail($id);
            Log::info("Searching service id", [$service]);
            return response()->json([
                "data" => $service
            ], 200);
        } catch(\Exception $e) {
            Log::info("Service not found", [$id]);
            return response()->json([
                "message" => "Servico não encontrado."
            ], 200);
        }
    }

    public function create(Request $request)
    {
        Log::info("Creating service");
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required',
            'duration' => 'required',
            'serviceCategory_id' => 'required|integer',
            'organization_id' => 'required|integer',
        ]);
        $services = Service::create($request->all());
        if($services->save()) {
            Log::info("Service created", [$services]);
            return response()->json([
                "message" => "Servico criado com sucesso",
            ], 200);
        } else {
            Log::error("Error create service", [$request]);
            return response()->json([
                "message" => "Erro ao criar servico. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function update(Request $request, Service $service)
    {
        $allowedTypes = ['a', 's', 'g'];
        if (!in_array($request->user()->type, $allowedTypes)) {
            Log::info("User without permission tried to update service", [$request->user()]);
            return response()->json([
                "message" => "Você não tem permissão para atualizar servicos.",
            ], 401);
        }
        Log::info("Updating service", [$request->id]);
        $service->update($request->all());
        if($service->save()) {
            return response()->json([
                "message" => "Servico atualizada com sucesso",
            ], 200);
        } else {
            Log::info("Error updating service", [$request]);
            return response()->json([
                "message" => "Erro ao atualizar servico. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function delete(Request $request, $id)
    {
        $allowedTypes = ['a', 's', 'g'];
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $service = Service::findOrFail($id); 
                Log::info("Inativation of the service", [$service]);
                $service->status = 0;
                $service->save();
                Log::info("Service inactivated successfully");
                return response()->json([
                    "message" => "Servico inativado com sucesso.",
                ], 200);
            } catch(\Exception $e) {
                Log::error("Error inativation of the service $id");
                return response()->json([
                    "message" => "Erro ao inativar servico. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission", [$request]);
            return response()->json([
                "message" => "Usuário sem permissão para inativar servico.",
            ], 400);
        }
    }
}
