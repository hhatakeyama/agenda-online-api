<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategories extends Model
{
    protected $fillable = [
    "name",
    "organization_id",
    "status",
    ];

    public function organization(){
        return $this->hasMany("App\Organizations", "organization_id", "id");
    }
}
