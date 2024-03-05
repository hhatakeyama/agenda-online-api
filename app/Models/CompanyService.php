<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyService extends Model
{
    protected $table = 'company_services';

    protected $fillable = [
        "company_id",
        "service_id",
        "price",
        "duration",
        "description",
        "send_email",
        "send_sms",
        "email_message",
        "sms_message",
    ];
    
    public function company(){
        return $this->belongsTo("App\Models\Company");
    }

    public function service(){
        return $this->belongsTo("App\Models\Service");
    }
}