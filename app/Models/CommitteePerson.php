<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommitteePerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'phone',
        'state',
        'image_path',
        'post_name',
        'sort_order',
        'status',
        'password', // Added for authentication
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }
}