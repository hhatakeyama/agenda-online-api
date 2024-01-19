<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule_Item extends Model
{
    use HasFactory;

    protected $fillable = [
        "schedule_id",
        "service_id",
        "employee_id",
        "date",
        "start_time",
        "end_time",
    ];

    public function schedule(){
        return $this->belongsTo("App\Models\Schedule");
    }

    public function service(){
        return $this->belongsTo("App\Models\Services");
    }

    public function employee(){
        return $this->belongsTo("App\Models\Employees");
    }
}
