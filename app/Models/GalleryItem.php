<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id', 'title', 'description', 'image_paths', 'video_paths', 'status',
    ];

    protected $casts = [
        'status' => 'string',
        'image_paths' => 'array', // Cast JSON to PHP array
        'video_paths' => 'array',
    ];

    protected $appends = [
        'image_paths_url',
        'video_paths_url',
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }
    
    // Accessor to get the first image path for backward compatibility
    public function getImagePathAttribute()
    {
        return isset($this->image_paths[0]) ? $this->image_paths[0] : null;
    }
    
    // Accessor to get full image URLs
    public function getImagePathsUrlAttribute()
    {
        if (!$this->image_paths) {
            return null;
        }
        
        $appUrl = config('app.url');
        return array_map(function ($path) use ($appUrl) {
            return $appUrl . '/storage/' . $path;
        }, $this->image_paths);
    }
    
    // Accessor to get the first image path as full URL
    public function getImagePathUrlAttribute()
    {
        $firstImagePath = $this->image_path;
        if ($firstImagePath) {
            return config('app.url') . '/storage/' . $firstImagePath;
        }
        return null;
    }
    
    // Mutator to set image paths from array
    public function setImagePathsAttribute($value)
    {
        $this->attributes['image_paths'] = json_encode($value);
    }

    // Accessor to get the first video path for backward compatibility
    public function getVideoPathAttribute()
    {
        return isset($this->video_paths[0]) ? $this->video_paths[0] : null;
    }
    
    // Accessor to get full video URLs
    public function getVideoPathsUrlAttribute()
    {
        if (!$this->video_paths) {
            return null;
        }
        
        $appUrl = config('app.url');
        return array_map(function ($path) use ($appUrl) {
            return $appUrl . '/storage/' . $path;
        }, $this->video_paths);
    }
    
    // Accessor to get the first video path as full URL
    public function getVideoPathUrlAttribute()
    {
        $firstVideoPath = $this->video_path;
        if ($firstVideoPath) {
            return config('app.url') . '/storage/' . $firstVideoPath;
        }
        return null;
    }
}