<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id',
        'title',
        'slug',
        'image_path',
        'keywords',
        'summary',
        'author',
        'posted_date',
        'status',
    ];

    protected $casts = [
        'posted_date' => 'date',
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

    public static function boot(){
        parent::boot();
         
        static::creating(function ($news){
            if (empty($news->slug)){
                $news->slug = $news->generateUniqueSlug($news->title);
            }
        });
        static::updating(function ($news){
            if (empty($news->slug)){
                $news->slug = $news->generateUniqueSlug($news->title, $news->id);
            }
        });
    }

    public function generateUniqueSlug($title, $id = null){
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        return $slug;
    }
    public function viewers()
    {
        return $this->belongsToMany(Customer::class,
        'news_views',
        'news_id',
        'user_id');
    }
}
