<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        "name",
        "description",
        "organization_id",
        "serviceCategory_id",
        "price",
        "duration",
        'send_email',
        'send_sms',
        'email_message',
        'sms_message',
        "status",
    ];

    public function organization(){
        return $this->belongsTo("App\Models\Organization", "organization_id", "id");
    }

    public function serviceCategory(){
        return $this->hasOne("App\Models\ServiceCategory", "id", "serviceCategory_id");
    }

    public function employeeServices(){
        return $this->hasMany("App\Models\EmployeeService", "service_id", "id");
    }
}
