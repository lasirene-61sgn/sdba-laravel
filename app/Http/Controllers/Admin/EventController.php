<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\RealTimeNotificationService;
use App\Services\NotificationsService;
use App\Models\Event;
use App\Models\EventRSVP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // Get the ID of the currently logged-in Admin
    private function getAdminId()
    {
        // Adjust 'admin' to your guard name if different
        return Auth::guard('admin')->id(); 
    }

    /**
     * Display a listing of the resource (ONLY for the logged-in admin).
     */
    public function index()
    {
        $events = Event::where('admin_id', $this->getAdminId())
                       ->orderBy('posted_date', 'desc')
                       ->paginate(10); 
                                   
        return view('admin.event.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.event.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, RealTimeNotificationService $pushService)
    {
        $request->validate([
            'images' => 'required|array|min:1', // Require at least one image
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate each image
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'posted_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        // Store all uploaded images
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('event_images', 'public');
            $imagePaths[] = $imagePath;
        }

        
             // Assign the current admin's ID
            $adminId = $this->getAdminId();
            $event = \App\Models\Event::create([
            'admin_id' => $this->getAdminId(),
            'name' => $request->name,
            'description' => $request->description,
            'image_paths' => $imagePaths, // Store array of image paths
            'posted_date' => $request->posted_date,
            'status' => $request->status,
            ]);
            if($event->status == 'active'){
                // Use the NotificationsService to create both in-app and real-time notifications
                $notificationService = new NotificationsService($pushService);
                $notificationService->createEventAddedNotification($adminId, $event);
            }

        return redirect()->route('admin.event.index')->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Authorization check: Ensure admin can only edit their own entries
        if ($event->admin_id !== $this->getAdminId()) {
            abort(403);
        }

        return view('admin.event.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event, NotificationsService $notificationService)
    {
        // Authorization check
        if ($event->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $request->validate([
            'images' => 'nullable|array', // Images are optional for update
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate each image
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'posted_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'posted_date' => $request->posted_date,
            'status' => $request->status,
        ];

        // Handle image uploads if new images are provided
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($event->image_paths as $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
            
            // Store new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('event_images', 'public');
                $imagePaths[] = $imagePath;
            }
            
            $updateData['image_paths'] = $imagePaths;
        }

        $event->update($updateData);
        
        // Create notifications for customers when event is updated
        $notificationService->createEventAddedNotification($this->getAdminId(), $event);

        return redirect()->route('admin.event.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Authorization check
        if ($event->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        // Delete all images
        foreach ($event->image_paths as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
        
        $event->delete();
        
        return redirect()->route('admin.event.index')->with('success', 'Event deleted successfully.');
    }
    
    /**
     * Display RSVP details for an event
     */
    public function rsvpDetails(Event $event)
    {
        // Authorization check: Ensure admin can only view their own event RSVPs
        if ($event->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        // Get all RSVPs for this event with customer details
        $rsvps = EventRSVP::where('event_id', $event->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.event.rsvp_details', compact('event', 'rsvps'));
    }
    
    /**
     * Display attendance details for an event (only those who actually attended)
     */
    public function attendance(Event $event)
    {
        // Authorization check: Ensure admin can only view their own event attendance
        if ($event->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        // Get only those who actually attended (attended = true) for this event with customer details
        $rsvps = EventRSVP::where('event_id', $event->id)
            ->where('attended', true)
            ->with('customer')
            ->orderBy('attendance_timestamp', 'desc')
            ->paginate(20);
        
        $title = 'Attendance';
        $type = 'attendance';
        
        return view('admin.event.attendance', compact('event', 'rsvps', 'title', 'type'));
    }
    
    /**
     * Display RSVP reports for an event based on type
     */
    public function rsvpReports(Event $event, Request $request)
    {
        // Authorization check: Ensure admin can only view their own event RSVPs
        if ($event->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $type = $request->query('type', 'accepted');
        
        // Set status based on type
        $status = 'accepted';
        $title = 'Accepted';
        
        switch ($type) {
            case 'rejected':
                $status = 'declined';
                $title = 'Rejected';
                break;
            case 'not-seen':
                $status = 'maybe';
                $title = 'Not Seen';
                break;
        }
        
        // Get RSVPs for this event with customer details based on status
        $rsvps = EventRSVP::where('event_id', $event->id)
            ->where('status', $status)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.event.rsvp_reports', compact('event', 'rsvps', 'title', 'type'));
    }
}