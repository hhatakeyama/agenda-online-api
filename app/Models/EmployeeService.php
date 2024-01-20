<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeService extends Model
{
    protected $table = 'employee_services';

    protected $fillable = [
        "employee_id",
        "service_id",
    ];

    public function employee(){
        return $this->belongsTo("App\Models\User");
    }

    public function service(){
        return $this->belongsTo("App\Models\Service");
    }

}
