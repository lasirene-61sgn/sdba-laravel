<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'social_link';

    protected $fillable = [
        'admin_id',
        'whatsapp_link',
        'facebook_link',
        'email_link',
        'twitter_link',
        'instagram_link',
        'linkedin_link',
        'youtube_link'
    ];
}
