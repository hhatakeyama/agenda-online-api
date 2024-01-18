<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Users;
use App\Models\Employee_Services;

class EmployeeController extends Controller
{
    public function get()
    {
        Log::info("Searching all employees");
        $employees = Users::paginate(10);;
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
        Log::info("Creating employee", [$request]);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'occupation' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
            'type' => 'required',
            'organization_id' => 'required|integer',
        ]);
        $employee = Users::create($request->all());
        $employee->password = Hash::make($request->password);
        if($employee->save()) {
            Log::info("Employee created", [$employee]);
            foreach($request->services as $service_id) {
                $this->createServicesEmployee($employee->id, $service_id);
            }
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

    public function createServicesEmployee($employee_id, $service_id)
    {
        Log::info("Service's employee", [$service_id]);
        $employee_service = Employee_Services::create([
            'employee_id' => $employee_id,
            'service_id' => $service_id,
        ]);
        if($employee_service->save()){
            Log::info("Service's employee created", [$employee_service]);
        } else {
            Log::error("Error create service's employee", [$request]);
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
