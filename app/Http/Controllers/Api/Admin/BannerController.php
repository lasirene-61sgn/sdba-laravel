<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of banners
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $banners = Banner::where('admin_id', $adminId)
                        ->latest()
                        ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $banners,
        ]);
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request, \App\Services\NotificationsService $notificationService)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'image_path' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validatedData['admin_id'] = $adminId;
        $banner = Banner::create($validatedData);
        
        // Create notifications for customers
        $notificationService->createBannerAddedNotification($adminId, $banner);

        return response()->json([
            'status' => 'success',
            'message' => 'Banner created successfully',
            'data' => $banner,
        ], 201);
    }

    /**
     * Display the specified banner
     */
    public function show(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $banner = Banner::where('admin_id', $adminId)
                       ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $banner,
        ]);
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $banner = Banner::where('admin_id', $adminId)
                       ->findOrFail($id);

        $validatedData = $request->validate([
            'image_path' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $banner->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Banner updated successfully',
            'data' => $banner,
        ]);
    }

    /**
     * Remove the specified banner
     */
    public function destroy(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $banner = Banner::where('admin_id', $adminId)
                       ->findOrFail($id);

        // Delete image if exists
        if ($banner->image_path && Storage::exists($banner->image_path)) {
            Storage::delete($banner->image_path);
        }

        $banner->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Banner deleted successfully',
        ]);
    }
}