<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDaysOfWeek extends Model
{
    protected $table = 'company_days_of_weeks';

    protected $fillable = [
        "day_of_week",
        "start_time",
        "end_time",
        "start_time_2",
        "end_time_2",
        "start_time_3",
        "end_time_3",
        "start_time_4",
        "end_time_4",
        "company_id",
    ];

    public function company(){
        return $this->belongsTo("App\Models\Company");
    }
}