<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password', 'company_name', 'image', 'sidebar_permissions', 'customer_field_permissions', 'helpline',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $casts = [
        'sidebar_permissions' => 'array',
        'customer_field_permissions' => 'array',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
    
    public function aboutUs()
    {
        return $this->hasOne(AboutUs::class);
    }
}