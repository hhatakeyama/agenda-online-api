<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Users;

class EmployeeController extends Controller
{
    public function get()
    {
        Log::info("Searching all employees");
        $employees = Users::all();
        return response()->json([
            "data" => $employees
        ], 200);
    }

    public function getById($id)
    {
        try {
            $employee = Users::findOrFail($id);
            Log::info("Searching employee id ", [$employee]);
            return response()->json([
                "data" => $employee
            ], 200);
        } catch(\Exception $e) {
            Log::info("Employee not found", [$id]);
            return response()->json([
                "message" => "Funcionário não encontrado."
            ], 200);
        }
    }

    public function create(Request $request)
    {
        Log::info("Creating employee");
        $employees = Users::create($request->all());
        if($employees->save()) {
            Log::info("Employee created", [$employees]);
            return response()->json([
                "message" => "Funcionario criado com sucesso",
            ], 200);
        } else {
            Log::error("Error create employee", [$request]);
            return response()->json([
                "message" => "Erro ao criar servico. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function update(Request $request, Users $employee)
    {
        Log::info("Updating employee", [$request->id]);
        $employee->update($request->all());
        if($employee->save()) {
            return response()->json([
                "message" => "Funcionario atualizada com sucesso",
            ], 200);
        } else {
            Log::info("Error updating employee", [$request]);
            return response()->json([
                "message" => "Erro ao atualizar funcionario. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    public function delete($id)
    {
        try {
            $employee = Users::findOrFail($id);  
            Log::info("Inativation of the employee $employee");
            $employee->status = false;
            $employee->save();
            Log::info("Employee inactivated successfully");
            return response()->json([
                "message" => "Funcionario inativado com sucesso.",
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error inativation of the employee $id");
            return response()->json([
                "message" => "Erro ao inativar funcionario. Entre em contato com o administrador do site.",
            ], 400);
        }
    }
}
