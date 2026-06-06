<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GalleryItem;
use App\Services\NotificationsService;

class GalleryItemController extends Controller
{
    /**
     * Display a listing of gallery items
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $galleryItems = GalleryItem::where('admin_id', $adminId)
                                 ->latest()
                                 ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $galleryItems,
        ]);
    }

    /**
     * Store a newly created gallery item
     */
    public function store(Request $request, NotificationsService $notificationService)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_paths' => 'required|array',
            'image_paths.*' => 'string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validatedData['admin_id'] = $adminId;
        $galleryItem = GalleryItem::create($validatedData);
        
        // Create notifications for customers
        $notificationService->createGalleryAddedNotification($adminId, $galleryItem);

        return response()->json([
            'status' => 'success',
            'message' => 'Gallery item created successfully',
            'data' => $galleryItem,
        ], 201);
    }

    /**
     * Display the specified gallery item
     */
    public function show(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $galleryItem = GalleryItem::where('admin_id', $adminId)
                                ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $galleryItem,
        ]);
    }

    /**
     * Update the specified gallery item
     */
    public function update(Request $request, $id, NotificationsService $notificationService)
    {
        $adminId = $request->user()->id;
        
        $galleryItem = GalleryItem::where('admin_id', $adminId)
                                ->findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image_paths' => 'sometimes|array',
            'image_paths.*' => 'string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $galleryItem->update($validatedData);
        
        // Create notifications for customers when gallery item is updated
        $notificationService->createGalleryAddedNotification($adminId, $galleryItem);

        return response()->json([
            'status' => 'success',
            'message' => 'Gallery item updated successfully',
            'data' => $galleryItem,
        ]);
    }

    /**
     * Remove the specified gallery item
     */
    public function destroy(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $galleryItem = GalleryItem::where('admin_id', $adminId)
                                ->findOrFail($id);

        $galleryItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Gallery item deleted successfully',
        ]);
    }
}