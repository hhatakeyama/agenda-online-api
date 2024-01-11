<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategories extends Model
{
    protected $fillable = [
    "name",
    "organizationId",
    "status",
    ];
}
