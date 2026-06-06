<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helpline extends Model
{
    protected $table = 'helplines';

    protected $fillable = [
        'admin_id', 'name','heading_name', 'mobile_numbers', 'whatsapp_numbers', 'emails', 'locations'
    ];

    protected $casts = [
        'mobile_numbers' => 'array',
        'whatsapp_numbers' => 'array',
        'emails' => 'array',
        'locations' => 'array'
    ];
}
