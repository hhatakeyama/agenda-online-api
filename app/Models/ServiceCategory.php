<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        "name",
        "organization_id",
        "status",
    ];

    public function organization(){
        return $this->belongsTo("App\Models\Organization", "organization_id", "id");
    }
}
