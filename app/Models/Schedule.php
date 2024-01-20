<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        "company_id",
        "client_id",
        "date",
    ];

    public function company(){
        return $this->belongsTo("App\Models\Company");
    }

    public function client(){
        return $this->belongsTo("App\Models\Client");
    }

    public function scheduleItems(){
        return $this->hasMany("App\Models\ScheduleItem");
    }
}
