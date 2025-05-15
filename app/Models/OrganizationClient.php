<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationClient extends Model
{
    protected $fillable = [
        "organization_id",
        "client_id",
        "status",
    ];
    
    public function client(){
        return $this->belongsTo("App\Models\Client", "client_id", "id");
    }
    
    public function organization(){
        return $this->belongsTo("App\Models\Organization", "organization_id", "id");
    }

}
