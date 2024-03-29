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
        "can_choose_random",
        "can_choose_employee",
        "can_simultaneous",
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

    public function companyServices(){
        return $this->hasMany("App\Models\CompanyService", "service_id", "id");
    }

    public function scheduleItems(){
        $today = date("Y-m-d");
        return $this->hasMany("App\Models\ScheduleItem", "service_id", "id")->whereHas("schedule", function($query) use ($today) {
            $query->where("date", ">=", $today);
        });
    }
}
