<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        "name",
        "region",
        "ibge_id",
    ];
    
    public function cities(){
        return $this->hasMany("App\Models\City", "state_id", "name");
    }
}
