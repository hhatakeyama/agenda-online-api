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
    "organization_id",
    "phone",
    "mobilePhone",
    "email",
    "socialMedia",
    "status"
    ];

    public function organization(){
        return $this->hasMany("App\Organizations", "organization_id", "id");
    }
}
