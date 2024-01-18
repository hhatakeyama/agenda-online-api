<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaysOfWeek_Company extends Model
{
    use HasFactory;
    protected $table = 'days_of_week_company';

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
        return $this->belongsTo("App\Models\Companies");
    }
}