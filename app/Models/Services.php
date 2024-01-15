<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $fillable = [
        "name",
        "description",
        "organization_id",
        "serviceCategoryId",
        "price",
        "duration",
        "status",
    ];

    public function organization(){
        return $this->hasMany("App\Organizations", "organization_id", "id");
    }

    public function serviceCategory(){
        return $this->hasMany("App\Services", "serviceCategoryId", "id");
    }
}
