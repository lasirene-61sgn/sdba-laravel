<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'message',
        'related_id',
        'related_type',
        'is_read',
        'read_at',
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
        'is_read' => 'boolean',
    ];
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
