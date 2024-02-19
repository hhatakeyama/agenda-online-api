<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\EmployeeService;
use App\Models\Organization;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching all employees", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $employees = [];
            if ($request->user()->type === 'g') {
                $employees = User::where("type", "f")
                    ->where(function ($subquery) use ($search) {
                        $subquery->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
                    })
                    ->where("organization_id", $request->user()->organization_id);
            } else {
                $employees = User::with("organization")
                    ->where("type", "f")
                    ->where(function ($subquery) use ($search) {
                        $subquery->where("name", "LIKE", "%$search%")->orWhere("email", "LIKE", "%$search%");
                    });
                if ($request->organization_id) {
                    $employees = $employees->where("organization_id", $request->organization_id);
                }
            }
            return response()->json([
                "data" => $employees->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching employee id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $employee = null;
                if ($request->user()->type === 'g') {
                    $employee = User::where("organization_id", $request->user()->organization_id)
                        ->where("type", "f")
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $employee = User::where("type", "f")
                        ->where("id", $id)
                        ->firstOrFail();
                }
                return response()->json(["data" => $employee], 200);
            } catch (\Exception $e) {
                Log::info("Employee not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Funcionário não encontrado."], 403);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function create(Request $request)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Creating employee", [$request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $request->validate([
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255',
                    'password' => 'required|max:255',
                    'occupation' => 'max:255',
                    'type' => 'required',
                    'organization_id' => 'required|integer',
                ]);
                $employee = User::create($request->all());
                $employee->password = Hash::make($request->password);
                if ($employee->save()) {
                    Log::info("Employee created");
                    if ($request->services) {
                        foreach ($request->services as $service_id) {
                            $this->createServicesEmployee($employee->id, $service_id);
                        }
                    }
                    $organization = Organization::find($request->organization_id);
                    $data = [
                        'name' => $employee->name,
                        'organization' => $organization->name,
                    ];
                    try {
                        Mail::send('mails.novofuncionario', $data, function ($message) use ($employee) {
                            $message->to($employee->email);
                            $message->subject('Skedyou - Novo usuário');
                            $message->from('suporte@skedyou.com', 'Equipe Skedyou');
                        });
                    } catch (\Exception $e) {
                        Log::error("Mail not sent", [$e->getMessage()]);
                    }
                    return response()->json(["message" => "Funcionário criado com sucesso"], 200);
                } else {
                    Log::error("Error create employee", [$request->all()]);
                    return response()->json([
                        "message" => "Erro ao criar servico. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                    ], 400);
                }
            } catch (\Exception $e) {
                Log::error("Error creating employee", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao criar funcionário. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function createServicesEmployee($employee_id, $service_id)
    {
        Log::info("Service's employee", [$service_id]);
        $employeeService = EmployeeService::create([
            'employee_id' => $employee_id,
            'service_id' => $service_id,
        ]);
        if ($employeeService->save()) {
            Log::info("Service's employee created");
        } else {
            Log::error("Error create service's employee", [$employee_id, $service_id]);
        }
    }

    public function update(Request $request, User $employee)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Updating employee", [$request->employee, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            $employee->update($request->all());
            if ($employee->save()) {
                Log::info("Employee updated");
                return response()->json(["message" => "Funcionário atualizado com sucesso"], 200);
            } else {
                Log::info("Error updating employee", [$request->all()]);
                return response()->json([
                    "message" => "Erro ao atualizar funcionário. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
                ], 400);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function delete(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Inativation of the employee", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $employee = User::where("type", "f")->findOrFail($id);
                $employee->status = 0;
                $employee->save();
                Log::info("Employee inactivated successfully");
                return response()->json(["message" => "Funcionário inativado com sucesso."], 200);
            } catch (\Exception $e) {
                Log::error("Error inativation of the employee", [$e->getMessage()]);
                return response()->json([
                    "message" => "Erro ao inativar funcionário. Entre em contato com o administrador do site.",
                ], 400);
            }
        } else {
            Log::info("Error updating employee");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}
