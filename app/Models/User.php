<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'occupation',
        'picture',
        'email',
        'password',
        'type',
        'organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function companyEmployees()
    {
        return $this->hasMany("App\Models\CompanyEmployee", "employee_id", "id");
    }

    public function employeeServices()
    {
        return $this->hasMany("App\Models\EmployeeService", "employee_id", "id");
    }

    public function scheduleItems()
    {
        return $this->hasMany("App\Models\ScheduleItem", "employee_id", "id");
    }
}
