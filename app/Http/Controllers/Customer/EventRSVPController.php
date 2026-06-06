<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventRSVP;

class EventRSVPController extends Controller
{
    /**
     * RSVP to an event
     */
    public function rsvp(Request $request, $eventId)
    {
        $customer = Auth::guard('customer')->user();
        
        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return redirect()->back()->with('error', 'Invalid customer data.');
        }
        
        // Validate the event exists and belongs to the same admin
        $event = Event::where('id', $eventId)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();
            
        if (!$event) {
            return redirect()->back()->with('error', 'Event not found or access denied.');
        }
        
        // Validate request data
        $rules = [
            'status' => 'required|in:accepted,declined,maybe',
            'note' => 'nullable|string|max:500'
        ];
        
        // Only require adults_count and children_count for accepted status
        if ($request->status === 'accepted') {
            $rules['adults_count'] = 'required|integer|min:0';
            $rules['children_count'] = 'required|integer|min:0';
        }
        
        $validatedData = $request->validate($rules);
        
        // Create or update RSVP
        $rsvp = EventRSVP::updateOrCreate(
            [
                'event_id' => $event->id,
                'customer_id' => $customer->id
            ],
            [
                'status' => $validatedData['status'],
                'note' => $validatedData['note'] ?? null,
                'adults_count' => $validatedData['adults_count'] ?? 0,
                'children_count' => $validatedData['children_count'] ?? 0
            ]
        );
        
        return redirect()->back()->with('success', 'RSVP recorded successfully.');
    }
    
    /**
     * Handle QR code attendance for events (separate from RSVP)
     */
    public function qrAttend($eventId)
    {
        // Check if customer is authenticated
        if (!Auth::guard('customer')->check()) {
            // If not authenticated, redirect to login with a message
            return redirect()->route('customer.login')->with('error', 'Please log in to mark your attendance.');
        }
        
        // Get the authenticated customer
        $customer = Auth::guard('customer')->user();
        
        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return redirect()->route('customer.event')->with('error', 'Invalid customer data.');
        }
        
        // Validate the event exists and belongs to the same admin
        $event = Event::where('id', $eventId)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();
            
        if (!$event) {
            return redirect()->route('customer.event')->with('error', 'Event not found or access denied.');
        }
        
        // Check if customer already has an attendance record for this event
        $existingAttendance = EventRSVP::where('event_id', $event->id)
            ->where('customer_id', $customer->id)
            ->where('attended', true)
            ->first();
            
        if ($existingAttendance) {
            // Already marked as attended
            return redirect()->route('customer.event')->with('success', 'You have already marked your attendance for this event!');
        }
        
        // Check if customer has an RSVP for this event
        $existingRsvp = EventRSVP::where('event_id', $event->id)
            ->where('customer_id', $customer->id)
            ->first();
            
        if ($existingRsvp) {
            // Update existing RSVP to mark as attended
            $existingRsvp->update([
                'attended' => true,
                'attendance_timestamp' => now(),
            ]);
        } else {
            // Create new attendance record (without RSVP)
            EventRSVP::create([
                'event_id' => $event->id,
                'customer_id' => $customer->id,
                'status' => 'accepted', // Default to accepted for attendance
                'adults_count' => 1,
                'children_count' => 0,
                'attended' => true,
                'attendance_timestamp' => now(),
            ]);
        }
        
        return redirect()->route('customer.event')->with('success', 'Your attendance has been recorded successfully!');
    }
    
    /**
     * Get RSVP status for a specific event
     */
    public function getRsvpStatus($eventId)
    {
        $customer = Auth::guard('customer')->user();
        
        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }
        
        // Validate the event exists and belongs to the same admin
        $event = Event::where('id', $eventId)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();
            
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found or access denied.'
            ], 404);
        }
        
        // Get RSVP status
        $rsvp = EventRSVP::where('event_id', $event->id)
            ->where('customer_id', $customer->id)
            ->first();
            
        return response()->json([
            'status' => 'success',
            'data' => $rsvp
        ]);
    }
}