<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaysOfWeek_Company extends Model
{
    use HasFactory;
    protected $fillable = [
        "day_of_week",
        "start_time",
        "end_time",
        "start_time_2",
        "end_time_2",
        "company_id",
    ];  
}
