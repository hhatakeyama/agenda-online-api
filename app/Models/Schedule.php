<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        "date",
        "company_id",
        "client_id",
        "confirmed",
        "done",
    ];

    public function company(){
        return $this->belongsTo("App\Models\Company", "company_id", "id");
    }

    public function client(){
        return $this->belongsTo("App\Models\Client", "client_id", "id");
    }

    public function scheduleItems(){
        return $this->hasMany("App\Models\ScheduleItem", "schedule_id", "id");
    }
}
