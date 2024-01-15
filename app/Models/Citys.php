<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citys extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "path",
        "priority",
        "state_id",
        "ibge_id",
    ];

    public function state(){
        return $this->hasMany("App\States", "state_id", "id");
    }
}
