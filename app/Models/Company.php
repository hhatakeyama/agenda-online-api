<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        "name",
        "address",
        "district",
        "cep",
        "city_id",
        "state",
        "thumb",
        "organization_id",
        "phone",
        "mobilePhone",
        "email",
        "socialMedia",
        "map",
        "status"
    ];

    public function organization(){
        return $this->belongsTo("App\Models\Organization", "organization_id", "id");
    }

    public function companyEmployees(){
        return $this->hasMany("App\Models\CompanyEmployee", "company_id", "id");
    }

    public function employeesService(){
        return $this->hasMany("App\Models\EmployeeService", "service_id", "id");
    }

    public function companyServices(){
        return $this->hasMany("App\Models\CompanyService", "company_id", "id");
    }

    public function daysOfWeeks(){
        return $this->hasMany("App\Models\CompanyDaysOfWeek", "company_id", "id");
    }

    public function city(){
        return $this->hasOne("App\Models\City", "id", "city_id");
    }
}
