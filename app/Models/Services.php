<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $fillable = [
        "name",
        "description",
        "organizationId",
        "serviceCategoryId",
        "price",
        "duration",
        "status",
    ];

    public function organization(){
        return $this->hasMany("App\Organizations", "organizationId", "id");
    }

    public function serviceCategory(){
        return $this->hasMany("App\Services", "serviceCategoryId", "id");
    }
}
