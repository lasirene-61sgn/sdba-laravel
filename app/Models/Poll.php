<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'admin_id',
        'description',
        'active'
    ];
    
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    public function responses()
    {
        return $this->hasMany(PollResponse::class);
    }
    
    public function getYesCountAttribute()
    {
        return $this->responses()->where('response', 'yes')->count();
    }
    
    public function getNoCountAttribute()
    {
        return $this->responses()->where('response', 'no')->count();
    }
    
    public function getMaybeCountAttribute()
    {
        return $this->responses()->where('response', 'maybe')->count();
    }
    
    public function getTotalResponsesAttribute()
    {
        return $this->responses()->count();
    }
}