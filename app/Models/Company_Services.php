<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_Services extends Model
{
    use HasFactory;
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
        return $this->belongsTo("App\Models\Companies");
    }

    public function service(){
        return $this->belongsTo("App\Models\Services");
    }
}