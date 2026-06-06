<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Support extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'image',
        'phone',
        'support_type_id',
        'support_category_id',
        'admin_id', // Add admin_id to track which admin created the support entry
        'status', // Add status field
    ];
    
    /**
     * Get the admin that owns this support.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    /**
     * Get the support type that owns this support.
     */
    public function supportType()
    {
        return $this->belongsTo(SupportType::class, 'support_type_id');
    }
    
    /**
     * Get the support category that owns this support.
     */
    public function supportCategory()
    {
        return $this->belongsTo(SupportCategory::class, 'support_category_id');
    }
}