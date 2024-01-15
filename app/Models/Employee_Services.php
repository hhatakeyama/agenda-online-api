<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee_Services extends Model
{
    use HasFactory;
    protected $fillable = [
        "employee_id",
        "service_id",
    ]; 
}
