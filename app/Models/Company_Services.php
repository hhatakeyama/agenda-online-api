<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_Services extends Model
{
    use HasFactory;
    protected $fillable = [
        "company_id",
        "service_id",
        "price",
        "duration",
        "description",
    ]; 
}