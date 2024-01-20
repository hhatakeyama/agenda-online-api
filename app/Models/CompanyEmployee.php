<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyEmployee extends Model
{
    protected $table = 'company_employees';

    protected $fillable = [
        "company_id",
        "employee_id",
    ];

    public function company(){
        return $this->belongsTo("App\Models\Company");
    }
    
    public function employee(){
        return $this->belongsTo("App\Models\User");
    }

}
