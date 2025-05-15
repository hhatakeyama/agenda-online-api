<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        "registeredName",
        "tradingName",
        "slug",
        "cnpj",
        "status",
    ];
    
    public function organizationClients(){
        return $this->hasMany("App\Models\OrganizationClient", "organization_id", "id");
    }

}
