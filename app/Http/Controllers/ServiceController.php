<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Services;

class ServiceController extends Controller
{
    public function get()
    {
        Log::info("Searching all services");
        $services = Services::paginate(10);;
        return response()->json([
            "data" => $services
        ], 200);
    }

    public function getById($id)
    {
        try {
            $service = Services::findOrFail($id);
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
            'serviceCategoryId' => 'required|integer',
            'organization_id' => 'required|integer',
        ]);
        $services = Services::create($request->all());
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

    public function update(Request $request, Services $service)
    {
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

    public function delete($id)
     {
        try {
            $service = Services::findOrFail($id); 
            Log::info("Inativation of the service $service");
            $service->status = false;
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
    }
}
