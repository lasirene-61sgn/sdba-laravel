<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollResponse extends Model
{
    protected $fillable = [
        'poll_id',
        'customer_id',
        'response'
    ];
    
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}