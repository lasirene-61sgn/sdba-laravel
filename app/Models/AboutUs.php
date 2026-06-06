<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AboutUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'description',
        'image_path',
        'vision',
        'mission',
    ];

    protected $table = 'about_us';

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}