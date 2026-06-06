<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Services\NotificationsService;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $events = Event::where('admin_id', $adminId)
                      ->latest()
                      ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $events,
        ]);
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request, NotificationsService $notificationService)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image_paths' => 'required|array',
            'image_paths.*' => 'string|max:255',
            'posted_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $validatedData['admin_id'] = $adminId;
        $event = Event::create($validatedData);
        
        // Create notifications for customers
        $notificationService->createEventAddedNotification($adminId, $event);

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully',
            'data' => $event,
        ], 201);
    }

    /**
     * Display the specified event
     */
    public function show(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $event = Event::where('admin_id', $adminId)
                     ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $event,
        ]);
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, $id, NotificationsService $notificationService)
    {
        $adminId = $request->user()->id;
        
        $event = Event::where('admin_id', $adminId)
                     ->findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image_paths' => 'sometimes|array',
            'image_paths.*' => 'string|max:255',
            'posted_date' => 'sometimes|date',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $event->update($validatedData);
        
        // Create notifications for customers when event is updated
        $notificationService->createEventAddedNotification($adminId, $event);

        return response()->json([
            'status' => 'success',
            'message' => 'Event updated successfully',
            'data' => $event,
        ]);
    }

    /**
     * Remove the specified event
     */
    public function destroy(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $event = Event::where('admin_id', $adminId)
                     ->findOrFail($id);

        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully',
        ]);
    }
}