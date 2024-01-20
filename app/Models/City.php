<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        "name",
        "path",
        "priority",
        "state_id",
        "ibge_id",
    ];

    public function state(){
        return $this->hasOne("App\Models\State", "state_id", "id");
    }
}
