<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleItem extends Model
{
    protected $table = 'schedule_items';
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
        return $this->belongsTo("App\Models\Schedule", "schedule_id", "id");
    }

    public function service(){
        return $this->belongsTo("App\Models\Service", "service_id", "id");
    }

    public function employee(){
        return $this->belongsTo("App\Models\User", "employee_id", "id");
    }
}
