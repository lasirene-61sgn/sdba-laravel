<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRSVP extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_rsvps';

    protected $fillable = [
        'event_id',
        'customer_id',
        'status',
        'note',
        'adults_count',
        'children_count',
        'attended',
        'attendance_timestamp',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'adults_count' => 'integer',
        'children_count' => 'integer',
        'attended' => 'boolean',
        'attendance_timestamp' => 'datetime',
    ];

    /**
     * Get the event associated with the RSVP
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the customer who made the RSVP
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}