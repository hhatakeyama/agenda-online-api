<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    protected $fillable = [
    "name",
    "address",
    "district",
    "cep",
    "city",
    "state",
    "thumb",
    "organization_id",
    "phone",
    "mobilePhone",
    "email",
    "socialMedia",
    "status"
    ];

    public function organization(){
        return $this->hasMany("App\Models\Organizations", "organization_id", "id");
    }

    public function companyEmployees(){
        return $this->hasMany("App\Models\Company_Employee", "company_id", "id");
    }

    public function companyServices(){
        return $this->hasMany("App\Models\Company_Services", "company_id", "id");
    }

    public function daysOfWeek(){
        return $this->hasMany("App\Models\DaysOfWeek_Company", "company_id", "id");
    }

    public function city(){
        return $this->hasOne("App\Models\Cities", "id", "city_id");
    }
}
