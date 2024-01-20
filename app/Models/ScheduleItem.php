<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleItem extends Model
{
    protected $fillable = [
        "schedule_id",
        "service_id",
        "employee_id",
        "start_time",
        "end_time",
        "price",
        "duration",
    ];

    public function schedule(){
        return $this->belongsTo("App\Models\Schedule");
    }

    public function service(){
        return $this->belongsTo("App\Models\Service");
    }

    public function employee(){
        return $this->belongsTo("App\Models\User");
    }
}
