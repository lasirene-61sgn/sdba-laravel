<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'image_path', 
        'status',
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }
    
    // Accessor to get full image URL
    public function getImagePathUrlAttribute()
    {
        if ($this->image_path) {
            return config('app.url') . '/storage/' . $this->image_path;
        }
        return null;
    }
}
