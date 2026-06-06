<?php

namespace App\Http\Controllers\Api\Customer;

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
        $customer = Auth::guard('sanctum')->user();
        
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
        
        return response()->json([
            'status' => 'success',
            'message' => 'RSVP recorded successfully.',
            'data' => $rsvp
        ]);
    }
    
    /**
     * Get RSVP status for a specific event
     */
    public function getRsvpStatus(Request $request, $eventId)
    {
        $customer = Auth::guard('sanctum')->user();
        
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