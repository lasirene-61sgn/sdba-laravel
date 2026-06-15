<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Event extends Model
{
    protected $fillable = [
        'admin_id',
        'name',
        'description',
        'date',
        'status',
        'image_paths',
        'posted_date',
    ];

    protected $casts = [
        'date' => 'date',
        'posted_date' => 'date',
        'image_paths' => 'array',
    ];

    /**
     * Get the admin that owns the event
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get all RSVPs for this event
     */
    public function rsvps()
    {
        return $this->hasMany(EventRSVP::class);
    }

    /**
     * Get the count of accepted RSVPs for this event
     */
    public function acceptedRsvpCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('status', 'accepted')
            ->count();
    }
    
    /**
     * Get the count of rejected RSVPs for this event
     */
    public function rejectedRsvpCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('status', 'declined')
            ->count();
    }
    
    /**
     * Get the count of not seen RSVPs for this event
     */
    public function notSeenRsvpCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('status', 'maybe')
            ->count();
    }
    
    /**
     * Get the total count of adults for accepted RSVPs
     */
    public function totalAdultsCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('status', 'accepted')
            ->sum('adults_count');
    }
    
    /**
     * Get the total count of children for accepted RSVPs
     */
    public function totalChildrenCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('status', 'accepted')
            ->sum('children_count');
    }
    
    /**
     * Get the count of attendees for this event
     */
    public function attendanceCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('attended', true)
            ->count();
    }
    
    /**
     * Get the total count of adults who attended
     */
    public function totalAttendedAdultsCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('attended', true)
            ->sum('adults_count');
    }
    
    /**
     * Get the total count of children who attended
     */
    public function totalAttendedChildrenCount()
    {
        return \App\Models\EventRSVP::where('event_id', $this->id)
            ->where('attended', true)
            ->sum('children_count');
    }
    
    /**
     * Generate QR code for event attendance
     */
    public function generateQrCode()
    {
        // Generate a unique URL for this event attendance
        // Using the correct parameter name as defined in the route
        $url = route('customer.event.qr-attend', ['eventId' => $this->id]);
        
        // Log the generated URL for debugging
        Log::info('Generating QR code for event ID: ' . $this->id . ' with URL: ' . $url);
        
        // Generate QR code as SVG
        return QrCode::size(300)->generate($url);
    }
    
    /**
     * Get QR code SVG for this event
     */
    public function getQrCodeAttribute()
    {
        return $this->generateQrCode();
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
        if (isset($this->image_paths[0])) {
            return config('app.url') . '/storage/' . $this->image_paths[0];
        }
        return null;
    }
    public function viewers()
    {
        return $this->belongsToMany(Customer::class,
        'event_views',
        'event_id',
        'user_id');
    }
}