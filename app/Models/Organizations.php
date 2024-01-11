<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizations extends Model
{
        protected $fillable = [
        "registeredName",
        "tradingName",
        "cnpj",
        "status",
    ];
}
