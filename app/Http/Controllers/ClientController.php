<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Clients;

class ClientController extends Controller
{
  public function get()
    {
        Log::info("Searching all clients");
        $clients = Clients::all();
        return response()->json([
            "data" => $clients
        ], 200);
    }

    public function getById($id)
    {
        try {
            $client = Clients::findOrFail($id);
            Log::info("Searching client id", [$client]);
            return response()->json([
                "data" => $client
            ], 200);
        } catch(\Exception $e) {
            Log::info("Client not found", [$id]);
            return response()->json([
                "message" => "Cliente não encontrado."
            ], 200);
        }
    }

    public function create(Request $request)
    {
        Log::info("Creating client");
        $uuid = Str::uuid('id')->toString();
        $clients = Clients::create($request->all());
        if($clients->save()) {
            Log::info("Client created", [$clients]);
            return response()->json([
                "message" => "Cliente criada com sucesso",
            ], 200);
        } else {
            Log::error("Error create client", [$request]);
            return response()->json([
                "message" => "Erro ao criar cliente. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function update(Request $request, Clients $client)
    {
        Log::info("Updating client", [$request]);
        $client->update($request->all());
        if($client->save()) {
            return response()->json([
                "message" => "Cliente atualizada com sucesso",
            ], 200);
        } else {
            Log::info("Error updating client", [$request]);
            return response()->json([
                "message" => "Erro ao atualizar cliente. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function delete($id)
    {
        try {
            $client = Clients::findOrFail($id); 
            Log::info("Inativation of the client $client");
            $client->status = false;
            $client->save();
            Log::info("Client inactivated successfully");
            return response()->json([
                "message" => "Cliente inativada com sucesso.",
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error inativation of the client $id");
            return response()->json([
                "message" => "Erro ao inativar cliente. Entre em contato com o administrador do site.",
            ], 400);
        }
    }
}
