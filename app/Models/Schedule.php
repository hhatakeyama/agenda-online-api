<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        "company_id",
        "client_id",
        "date",
    ];

    public function company(){
        return $this->belongsTo("App\Models\Companies");
    }

    public function client(){
        return $this->belongsTo("App\Models\Clients");
    }

    public function scheduleItems(){
        return $this->hasMany("App\Models\Schedule_Item");
    }
}
