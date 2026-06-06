<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportType extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'admin_id', // Add admin_id to track which admin created the support type
    ];
    
    /**
     * Get the admin that owns this support type.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    /**
     * Get all supports for this support type.
     */
    public function supports()
    {
        return $this->hasMany(Support::class, 'support_type_id');
    }
}