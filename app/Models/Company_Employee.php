<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_Employee extends Model
{
    use HasFactory;
    protected $table = 'company_employee';

    protected $fillable = [
        "company_id",
        "employee_id",
    ];

    public function employee(){
        return $this->belongsTo("App\Models\Users");
    }

    public function company(){
        return $this->belongsTo("App\Models\Companies");
    }
}
