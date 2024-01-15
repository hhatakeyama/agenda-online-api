<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        "company_id",
        "employee_id",
    ]; 
}
