<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    protected $fillable = [
    "name",
    "address",
    "district",
    "cep",
    "city",
    "state",
    "thumb",
    "organizationId",
    "phone",
    "mobilePhone",
    "email",
    "socialMedia",
    "status"
    ];

    public function organization(){
        return $this->hasMany("App\Organizations", "organizationId", "id");
    }
}
