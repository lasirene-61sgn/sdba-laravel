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
        'image_path',
        'post_name',
        'sort_order',
        'status',
        'password', // Added for authentication
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }
    public function viewers()
    {
        return $this->belongsToMany(
            Customer::class,
            'committee_views',
            'committee_person_id',
            'user_id',
        );
    }
}